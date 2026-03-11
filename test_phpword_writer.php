<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$templatePath = storage_path('app/public/kop-surat.docx');
$outputPath = storage_path('app/public/kop-surat-with-text.docx');

try {
    $phpWord = \PhpOffice\PhpWord\IOFactory::load($templatePath);
    
    // Get the first section, or add one if it doesn't exist
    $sections = $phpWord->getSections();
    if (count($sections) > 0) {
        $section = $sections[0];
    } else {
        $section = $phpWord->addSection();
    }
    
    // Define font styles
    $fontStyle = ['name' => 'Times New Roman', 'size' => 12];
    $boldStyle = ['name' => 'Times New Roman', 'size' => 12, 'bold' => true];
    
    // Add content
    $section->addTextBreak(1);
    
    $table = $section->addTable(['width' => 100 * 50, 'unit' => 'pct']);
    $table->addRow();
    $cell1 = $table->addCell(5000);
    $textRun = $cell1->addTextRun();
    $textRun->addText('Nomor : ', $boldStyle);
    $textRun->addText('${nomor_surat}', $fontStyle);
    
    $cell2 = $table->addCell(5000);
    $cell2->addText('Mojokerto, ${tanggal_surat_lengkap}', $fontStyle, ['align' => 'right']);
    
    $section->addTextBreak(2);
    $section->addText('Kepada Yth.', $fontStyle);
    $section->addText('Bapak/Ibu ${nama_nasabah}', $boldStyle);
    $section->addText('${alamat_nasabah}', $fontStyle);
    
    $section->addTextBreak(1);
    $textRun = $section->addTextRun();
    $textRun->addText('Perihal : ', $boldStyle);
    $textRun->addText('${jenis_surat}', $boldStyle);
    
    $section->addTextBreak(1);
    $section->addText('Dengan hormat,', $fontStyle);
    
    $textRun = $section->addTextRun(['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH]);
    $textRun->addText('Menunjuk Perjanjian Kredit No. ', $fontStyle);
    $textRun->addText('${nomor_pk}', $boldStyle);
    $textRun->addText(' tanggal ', $fontStyle);
    $textRun->addText('${tanggal_pk}', $boldStyle);
    $textRun->addText(' antara PT. BANK PERKREDITAN RAKYAT PURISEGER SENTOSA selanjutnya disebut Bank dengan ', $fontStyle);
    $textRun->addText('${nama_nasabah}', $boldStyle);
    $textRun->addText(' dan memperhatikan kondisi terakhir kredit, dengan ini kami sampaikan hal-hal sebagai berikut:', $fontStyle);
    
    $section->addTextBreak(1);
    $listStyle = ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED];
    $section->addListItem('Bahwa sampai saat ini saudara belum menyelesaikan tunggakan kewajiban kepada Bank sesuai dengan kesepakatan yang tercantum dalam Perjanjian kredit yang telah saudara tanda tangani.', 0, $fontStyle, $listStyle, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH]);
    
    $textRun = $section->addListItemRun(0, $listStyle, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH]);
    $textRun->addText('Jumlah tunggakan kewajiban saudara posisi tanggal ', $fontStyle);
    $textRun->addText('${tanggal_tunggakan}', $boldStyle);
    $textRun->addText(' adalah sebesar ', $fontStyle);
    $textRun->addText('Rp. ${jumlah_tunggakan},-', $boldStyle);
    $textRun->addTextBreak(1);
    $textRun->addText('Jumlah tunggakan tersebut akan terus bertambah sampai saudara melakukan penyelesaian.', $fontStyle);
    
    $textRun = $section->addListItemRun(0, $listStyle, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH]);
    $textRun->addText('Untuk menghindari pembebanan denda atas keterlambatan yang akan semakin memberatkan saudara, serta agar tidak menambah kerugian bagi Bank, kami sangat mengharapkan agar saudara segera menyelesaikan seluruh tunggakan dimaksud, paling lambat tanggal ', $fontStyle);
    $textRun->addText('${batas_waktu}', $boldStyle);
    $textRun->addText('.', $fontStyle);
    
    $section->addTextBreak(1);
    $section->addText('Demikian ${jenis_surat} ini kami sampaikan untuk menjadi perhatian saudara. Selanjutnya kami menunggu penyelesaian saudara. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.', $fontStyle, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH]);
    
    $section->addTextBreak(2);
    $section->addText('Hormat kami,', $fontStyle, ['align' => 'right']);
    $section->addText('Bank Perekonomian Rakyat', $fontStyle, ['align' => 'right']);
    $section->addText('PURISEGER SENTOSA', $fontStyle, ['align' => 'right']);
    $section->addTextBreak(3);
    $section->addText('[____________________]', $boldStyle, ['align' => 'right']);
    
    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save($outputPath);
    
    echo "Saved to $outputPath\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
