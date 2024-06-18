<?php
require_once __DIR__ . "/mpdf60/mpdf.php";

class PdfTemplateHandler
{
  public $template_loaded;
  public $template_folder;
  public $pages;
  public $mpdf;
  public $title;

  public function __construct($title, $pdf_format = 'A4', $display_mode = 'fullpage', $margins = null)
  {
    $this->mpdf = new mPDF('UTF-8', $pdf_format);

    if (isset($margins)) {
      $this->mpdf->SetMargins($margins->left, $margins->right, $margins->top);
      $this->mpdf->margin_footer = $margins->bottom;
      $this->mpdf->DeflMargin = $margins->left;
      $this->mpdf->DefrMargin = $margins->right;
    }

    $this->pages = [];
    $this->mpdf->SetTitle($title);
    $this->mpdf->SetDisplayMode($display_mode);
    $this->title = $title;
  }

  public function loadPageFile($file_name, $template_data)
  {
    ob_start();
    $template_url = __DIR__ . '/templates/' . $file_name;

    require $template_url;
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
  }

  public function addPage($template_page)
  {
    array_push($this->pages, $template_page);
  }

  public function preparePDF()
  {
    $this->mpdf->allow_charset_conversion = true;
    $this->mpdf->ignore_invalid_utf8 = true;

    for ($i = 0; $i < count($this->pages); $i++) {
      if ($i != 0) {
        $this->mpdf->AddPage();
      }

      $pagina = mb_convert_encoding($this->pages[$i], 'UTF-8');
      $this->mpdf->WriteHTML($pagina);
    }

    return $this->mpdf;
  }

  public function showPDF($file_name, $file_destination = 'I') {
    $this->mpdf->Output($file_name, $file_destination);
  }
}
