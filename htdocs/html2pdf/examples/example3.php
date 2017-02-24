<?php
/** $Id: example3.php 923 2003-11-18 17:18:40Z jrust $ */
/**
 * Here we create an encrypted PDF file based on a dynaically generated page. 
 * We buffer the content of the page and then create the PDF at the end.
 * Then we load up PDFEncryptor and set meta-data, password, and permissions.
 */

// Require the class
require_once dirname(__FILE__) . '/../HTML_ToPDF.php';
require_once dirname(__FILE__) . '/../PDFEncryptor.php';
// Create a unique filename for the resulting PDF
$linkToPDFFull = $linkToPDF = tempnam(dirname(__FILE__), 'PDF-');
// Remove the temporary file it creates
unlink($linkToPDFFull);
// Give it an extension
$linkToPDFFull .= '.pdf';
$linkToPDF .= '.pdf';
// Make it web accessible
$linkToPDF = basename($linkToPDF);
// Tempoary HTML file name
$htmlFile = str_replace('.pdf', '.htm', $linkToPDFFull); 
$defaultDomain = 'www.example.com';

// Buffer the current html page so we can write it to file later
ob_start();
?>
<html>
<head>
  <title>Testing HTML_ToPDF</title>
  <style type="text/css">
  div.noprint {
    display: none;
  }
  h6 {
    font-style: italic;
    font-weight: bold;
    font-size: 14pt;
    font-family: Courier;
    color: blue;
  }
  /** Change the paper size, orientation, and margins */
  @page {
    size: 8.5in 14in;
    orientation: landscape;
  }
  /** This is a bit redundant, but its works ;) */
  /** odd pages */
  @page:right {
    margin-right: 1.0cm;
    margin-left: 1.0cm;
    margin-top: 1.0cm;
    margin-bottom: 1.0cm;
  }
  /** even pages */
  @page:left {
    margin-right: 1.0cm;
    margin-left: 1.0cm;
    margin-top: 1.0cm;
    margin-bottom: 1.0cm;
  }
  </style>
</head>
<body>
  An example dynamic page that is converted to PDF on 8x14 paper, in landscape mode, with 1.0cm margins!<br /> 
  This document has been encrypted with the helper PDFEncryptor class so you will need to
  enter "foobar" for the password<br />
  Click <a href="<?php echo $linkToPDF; ?>">here</a> to view the PDF file.<br />
  <div class="noprint">This should not show up.</div>
  <h6>
  This demonstrates the use of CSS classes for an element.<br />
  What CSS properties and blocks can be used can be found at 
  <a href="http://www.tdb.uu.se/~jan/html2psug.html">http://www.tdb.uu.se/~jan/html2psug.html</a>
  </h6>
  Inserting a page break..<br /><br />
  <!--NewPage-->
  Now on to page 2!
</body>
</html>
<?php
// Write the buffered HTML file
$fp = fopen($htmlFile, 'w');
fwrite($fp, ob_get_contents());
fclose($fp);
ob_end_flush();

// Let the class generate a unique PDF filename
$pdf =& new HTML_ToPDF($htmlFile, $defaultDomain);
// Example setup if running under windows:
// $pdf->setHtml2Ps('perl c:\html2ps\bin\html2ps');
// $pdf->setPs2Pdf('c:\ghostscript\bin\ps2pdf');
// Could turn on debugging to see what exactly is happening
// (commands being run, images being grabbed, etc.)
// $pdf->setDebug(true);
// Convert the file
$result = $pdf->convert();

// Check if the result was an error
if (PEAR::isError($result)) {
    die($result->getMessage());
}
else {
    // Move the generated PDF to the web accessible file
    copy($result, $linkToPDFFull);
    unlink($result);
    // Remove the temporary html file
    unlink($htmlFile);

    // Set up encryption
    $encryptor =& new PDFEncryptor($linkToPDFFull);
    // Set paths
    $encryptor->setJavaPath('/usr/java/j2sdk1.4.1_02/bin/java');
    $encryptor->setITextPath(dirname(__FILE__) . '/../lib/itext-0.99.jar');
    // Set meta-data
    $encryptor->setAuthor('Paul Bunyan');
    $encryptor->setKeywords('HTML_ToPDF, php, encryption of PDF');
    $encryptor->setSubject('Example of HTML_ToPDF with Ecnryption');
    $encryptor->setTitle('Showing its stuff');
    // Set permissions
    $encryptor->setAllowPrinting(false);
    $encryptor->setAllowModifyContents(false);
    $encryptor->setAllowDegradedPrinting(true);
    $encryptor->setAllowCopy(true);
    // Set password
    $encryptor->setUserPassword('foobar');
    $encryptor->setOwnerPassword('barfoo');
    $result = $encryptor->encrypt();
    if (PEAR::isError($result)) {
        die($result->getMessage());
    }
}
