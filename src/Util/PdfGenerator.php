<?php

namespace App\Util;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGenerator
{
    private string $chrootDir;

    public function __construct(string $chrootDir)
    {
        $this->chrootDir = $chrootDir;
    }

    public function generate(string $htmlView, string $streamName, array $assetPathReplacementCallbacks = []): void
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);
        $dompdf->getOptions()->setChroot($this->chrootDir);

        $html = $htmlView;
        foreach ($assetPathReplacementCallbacks as $callback) {
            $html = $callback($html, $this->chrootDir);
        }

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream($streamName, [
            'Attachment' => false,
        ]);
    }
}
