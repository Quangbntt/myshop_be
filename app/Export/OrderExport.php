<?php

namespace App\Export;

use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class OrderExport implements FromCollection, WithHeadings, WithMapping, ShouldQueue, WithColumnFormatting, WithColumnWidths, WithStyles, WithEvents
{
    use Exportable;

    protected $order;

    public function __construct(Collection $order)
    {
        $this->order = $order;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->order;
    }

    public function headings(): array
    {
        return [
            'Tên',
            'Số điện thoại',
            'Địa chỉ',
            'Tên sản phẩm',
            // 'Hình ảnh',
            'Màu sắc',
            'Kích cỡ',
            'Giá bán',
            'Giá nhập',
            'Số lượng',
            'Lợi nhuận',
            'Ngày mua'
        ];
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->phone,
            $row->address,
            $row->product_name,
            // $row->product_image = "",
            $row->color,
            $row->size_name,
            $row->product_price,
            $row->product_cost,
            $row->orders_quantity,
            $row->orders_quantity*($row->product_price-$row->product_cost),
            $row->created_at => date('d-m-Y', strtotime($row->created_at)),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_NUMBER,
            'H' => NumberFormat::FORMAT_NUMBER,
            'I' => NumberFormat::FORMAT_NUMBER,
            'J' => NumberFormat::FORMAT_NUMBER,
            'K' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 20,
            'C' => 50,
            'D' => 35,
            'E' => 10,
            'F' => 10,
            'G' => 20,
            'H' => 10,
            'I' => 20,
            'J' => 20,
            'K' => 20,
            'L' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row Ds bold text.
            1    => ['font' => ['bold' => true]]
        ];
    }

    public function registerEvents(): array
    {
        return [
            // AfterSheet::class    => function(AfterSheet $event) {
            //     $row = $this->order;
            //     foreach ($row as $k => $v) {
            //         $drawing = new Drawing();
            //         $drawing->setName('Logo');
            //         $drawing->setDescription('This is my logo');
            //         $drawing->setPath(public_path('/test.png'));
            //         $drawing->setHeight(20);
            //         $drawing->setCoordinates('E'.($k+2));
            //         $event->sheet->addDrawings($drawing);
            //     }
            // },
        ];
    }
}
