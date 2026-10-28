<?php

namespace Tests\Feature;

use App\Exports\AssetsReportExport;
use App\Exports\AssetsSheet;
use App\Models\Asset;
use App\Models\Category;
use App\Models\User;
use App\Services\ReportService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $staff;

    protected User $viewer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->staff = User::where('email', 'staff@inventory.local')->first();
        $this->viewer = User::where('email', 'viewer@inventory.local')->first();
    }

    public function test_pdf_export_downloads_filtered_report(): void
    {
        $laptopCategory = Category::where('name', 'Laptop')->first();

        $response = $this->actingAs($this->staff)->get(route('reports.export.pdf', [
            'category_id' => $laptopCategory->id,
        ]));

        $response->assertOk();
        $response->assertDownload();
        $this->assertStringStartsWith('application/pdf', $response->headers->get('content-type'));
    }

    public function test_excel_export_downloads_filtered_report(): void
    {
        Excel::fake();
        Excel::matchByRegex();

        $laptopCategory = Category::where('name', 'Laptop')->first();

        $response = $this->actingAs($this->staff)->get(route('reports.export.excel', [
            'category_id' => $laptopCategory->id,
        ]));

        $response->assertOk();

        Excel::assertDownloaded(
            '/laporan-aset-\d{8}-\d{6}\.xlsx/',
            fn (AssetsReportExport $export) => (int) $export->filters['category_id'] === $laptopCategory->id
        );
    }

    public function test_excel_export_generates_a_real_xlsx_file(): void
    {
        $response = $this->actingAs($this->staff)->get(route('reports.export.excel'));

        $response->assertOk();
        $response->assertDownload();
        $this->assertStringContainsString('spreadsheetml', $response->headers->get('content-type'));
    }

    public function test_export_contains_two_sheets_and_only_filtered_assets(): void
    {
        $laptopCategory = Category::where('name', 'Laptop')->first();

        $export = new AssetsReportExport(
            ['category_id' => (string) $laptopCategory->id],
            app(ReportService::class)
        );

        $sheets = $export->sheets();

        $this->assertCount(2, $sheets);
        $this->assertInstanceOf(AssetsSheet::class, $sheets[0]);
        $this->assertSame('Data Aset', $sheets[0]->title());
        $this->assertSame('Ringkasan', $sheets[1]->title());
        $this->assertSame(
            Asset::where('category_id', $laptopCategory->id)->count(),
            $sheets[0]->query()->count()
        );
    }

    public function test_report_page_shows_export_actions(): void
    {
        $response = $this->actingAs($this->staff)->get(route('reports.index'));

        $response->assertOk();
        $response->assertSee(route('reports.export.pdf'));
        $response->assertSee(route('reports.export.excel'));
    }

    public function test_viewer_cannot_access_reports_or_exports(): void
    {
        $this->actingAs($this->viewer)->get(route('reports.index'))->assertForbidden();
        $this->actingAs($this->viewer)->get(route('reports.export.pdf'))->assertForbidden();
        $this->actingAs($this->viewer)->get(route('reports.export.excel'))->assertForbidden();
    }

    public function test_guest_is_redirected_from_report_exports(): void
    {
        $this->get(route('reports.export.pdf'))->assertRedirect(route('login'));
        $this->get(route('reports.export.excel'))->assertRedirect(route('login'));
    }

    public function test_invalid_report_filter_is_rejected(): void
    {
        $this->actingAs($this->staff)
            ->get(route('reports.index', ['status' => 'invalid-status']))
            ->assertSessionHasErrors('status');
    }

    public function test_end_date_filter_without_start_date_is_allowed(): void
    {
        $this->actingAs($this->staff)
            ->get(route('reports.index', ['end_date' => now()->toDateString()]))
            ->assertOk();
    }
}
