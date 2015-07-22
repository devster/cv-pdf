<?php

class Pdf extends TCPDF
{
    const PAGE_BORDER = 5;
    const INLINE_BORDER = 0.2;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $extraFonts = [];

    public function __construct($data)
    {
        $this->data = $data;
        parent::__construct($orientation='Portrait', $unit='mm', $format='A4');

        $this->SetCreator($data['meta']['creator']);
        $this->SetAuthor($data['meta']['author']);
        $this->SetTitle($data['meta']['title']);
        $this->SetSubject($data['meta']['subject']);

        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
        $this->SetMargins(0, 0, 0);
        $this->SetHeaderMargin(0);
        $this->SetFooterMargin(0);
        $this->SetAutoPageBreak(false);
        $this->setHtmlLinksStyle($data['colors']['links'], '');
        $this->setFontSubsetting(false);

        $this->AddPage();

        $this->drawPageGrid();
        $this->drawHeader();

        $this->SetY(59);

        $this->drawExperiences();
        $this->SetY($this->GetY() + 2);
        $this->drawEducation();

        $this->drawProfile();
        $this->drawSkills();
        $this->drawContact();
    }

    public function drawPageGrid()
    {
        $w = self::PAGE_BORDER;
        $this->SetLineStyle(['width' => $w, 'color' => $this->data['colors']['pageBorder']]);

        $this->Line(0,0,$this->getPageWidth(),0);
        $this->Line($this->getPageWidth(),0,$this->getPageWidth(),$this->getPageHeight());
        $this->Line(0,$this->getPageHeight(),$this->getPageWidth(),$this->getPageHeight());
        $this->Line(0,0,0,$this->getPageHeight());

        // vertical line
        $this->SetLineStyle(['width' => self::INLINE_BORDER, 'color' => $this->data['colors']['pageBorder']]);
        $this->Line(133, 50, 133, $this->getPageHeight());

        // header line
        $this->SetLineStyle(['width' => self::INLINE_BORDER, 'color' => $this->data['colors']['pageBorder']]);
        $this->Line(0, 50, $this->getPageWidth(), 50);

        // skills line
        $this->SetLineStyle(['width' => self::INLINE_BORDER, 'color' => $this->data['colors']['pageBorder']]);
        $this->Line(133, 136, 210, 136);

        // contact line
        $this->SetLineStyle(['width' => self::INLINE_BORDER, 'color' => $this->data['colors']['pageBorder']]);
        $this->Line(133, 239, $this->getPageWidth(), 239);
    }

    public function drawHeader()
    {
        $this->setFontSpacing(0.18);
        $x = 65;
        // Name
        $this->SetTextColorArray($this->data['colors']['dark']);
        $this->useFont('montserrat', 25, 'b');
        $this->Text($x, 17.75, strtoupper($this->data['name']));

        // title
        $this->SetTextColorArray($this->data['colors']['light']);
        $this->useFont('latolight', 12, '');
        $this->Text($x, 28.8, $this->data['title']);
        $this->setFontSpacing(0);

        // Portrait
        $this->Image(__DIR__.'/../resources/'.$this->data['image'], 26, 11, 30, 30);
    }

    public function drawExperiences()
    {
        $this->drawTitle($this->data['menu']['experience'], 11, $this->GetY());
        $this->SetY($this->GetY() + 13);

        foreach ($this->data['experiences'] as $d) {
            $this->SetX(10);
            $this->drawContent($d['title'], $d['date'], $d['content'], isset($d['link']) ? $d['link'] : null);
            $this->SetY($this->GetY()+4.5);
        }
    }

    public function drawEducation()
    {
        $this->drawTitle($this->data['menu']['education'], 11, $this->GetY());
        $this->SetY($this->GetY()+13);

        foreach ($this->data['education'] as $d) {
            $this->SetX(10);
            $this->drawContent($d['title'], $d['date'], $d['content'], isset($d['link']) ? $d['link'] : null);
            $this->SetY($this->GetY()+4.5);
        }
    }

    public function drawTitle($txt, $x, $y)
    {
        $fontsize = 14;
        $fontname = 'montserrat';
        $fontstyle = '';
        $txt = strtoupper($txt);

        $this->setFontSpacing(1);
        $w = $this->GetStringWidth($txt, $fontname, $fontstyle, $fontsize);
        $this->SetLineStyle(['width' => 0.3, 'color' => $this->data['colors']['dark']]);
        $this->SetXY($x, $y);
        $this->SetTextColorArray($this->data['colors']['dark']);
        $this->useFont($fontname, $fontsize, $fontstyle);
        $this->Cell($w+5.3, $h=7.8, $txt, $border=1, $ln=0, $align='C');
    }

    public function drawContent($title, $date, $content, $titleLink = '')
    {
        $date = ' / '.$date;

        $this->SetX($this->GetX()-1);
        $this->SetTextColorArray($this->data['colors']['dark']);

        $this->setFontSpacing(0.1);
        $this->useFont('lato', 14, 'b');
        $this->Cell($w=6.5, $h=0, '>', $border=0, $ln=0, $align='L', $fill=false, $link = '', $stretch=0, $ignore_min_height=false, $calign='T', $valign='T');

        $this->useFont('montserrat', 11);
        $w = $this->GetStringWidth($title);
        $this->Cell($w, $h=0, $title, $border=0, $ln=0, $align='L', $fill=false, $titleLink, $stretch=0, $ignore_min_height=false, $calign='T', $valign='B');

        $this->SetTextColorArray($this->data['colors']['light']);
        $this->useFont('lato', 9, '');
        $w = $this->GetStringWidth($date);
        $this->Cell($w, $h=4.8, $date, $border=0, $ln=0, $align='L', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='B');

        $content = str_replace('\n', "\n", $content);
        $this->setFontSpacing(0.05);
        $this->SetY($this->GetY()+6);
        $this->SetX($this->GetX()+15.5);
        $this->SetTextColorArray($this->data['colors']['textlight']);
        $this->useFont('latolight', 9, '');
        $this->MultiCell($w = 113, $h=0, $content, $border=0, $align='L', $fill=false, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);

    }

    public function drawProfile()
    {
        $this->SetXY(140, 59);

        $this->drawSubtitle($this->data['menu']['profile']);

        $this->setFontSpacing(0.05);
        $this->SetX($this->GetX()+140);
        $this->SetTextColorArray($this->data['colors']['textlight']);
        $this->useFont('latolight', 8.5, '');
        $this->MultiCell($w = 64, $h=0, $this->data['profile'], $border=0, $align='L', $fill=false, $ln=1, $x=140, $y=72, $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
    }

    public function drawContact()
    {
        $x = 140;
        $y = 246;
        $boxWidth = 17;

        $space = 3.5;
        $this->SetXY($x, $y);

        $this->drawSubtitle($this->data['menu']['contact']);

        $this->setFontSpacing(0.05);
        $this->useFont('latolight', 8, '');
        $this->SetLineStyle(['width' => 0.1, 'color' => $this->data['colors']['dark']]);
        $this->SetTextColorArray($this->data['colors']['textlight']);

        $y += 10;
        foreach ($this->data['contact'] as $label => $value) {
            $this->SetXY($x+1, $y);
            $this->Cell($boxWidth, $h=4, ' '.$label, $border=1, $ln=0, $align='L');
            $this->SetX($x+$boxWidth+$space);
            $this->Cell(0, $h=4, $value, $border=0, $ln=1, $align='L');
            $y += 6;
        }
    }

    public function drawSubtitle($txt)
    {
        $this->SetTextColorArray($this->data['colors']['dark']);
        $this->setFontSpacing(0.1);
        $this->useFont('montserrat', 11);
        $this->Cell($w=0, $h=0, $txt, $border=0, $ln=0, $align='L', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='B');
    }

    public function drawSkills()
    {
        $x = 140;
        $y = 142;
        $this->SetXY($x, $y);
        $this->drawSubtitle($this->data['menu']['skills']);
        $this->SetY($y+4);

        foreach ($this->data['skills'] as $d) {
            $y = $this->GetY()+4;
            $this->drawSkill($x, $y, $d['title'], $d['content']);
        }
    }

    public function drawSkill($x, $y, $title, $text)
    {
        $this->SetTextColorArray($this->data['colors']['light']);
        $this->setFontSpacing(0.1);
        $this->useFont('montserrat', 10);

        $this->Text($x, $y, $title);
        $y += 5;

        $this->SetTextColorArray($this->data['colors']['textlight']);
        $this->setFontSpacing(0.1);
        $this->useFont('latolight', 9);


        $this->MultiCell(65, $h=0, $text, $border=0, $align='L', $fill=false, $ln=1, $x, $y, $reseth=true, $stretch=0, $ishtml=true);
        // $y = $this->GetY();
        // $y += 1;

        // $pos = $x-2;
        // $pos += ($w/100) * $percent;

        // $this->SetLineStyle(['width' => 0.1, 'color' => $this->data['colors']['dark']]);
        // $this->Rect($x, $y, $w, 2.4);
        // $this->useFont('lato', 14, 'b');
        // $this->Text($pos, $y-2.2, '>');
    }

    protected function useFont($name, $size, $style = '')
    {
        $this->SetFont($name, $style, $size);
    }
}
