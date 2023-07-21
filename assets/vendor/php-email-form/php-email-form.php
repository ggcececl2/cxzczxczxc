<?php
// PHP Email Form Class
class PHP_Email_Form
{
  public $to;
  public $from_name;
  public $from_email;
  public $subject;
  public $smtp;

  private $ajax = false;

  public function add_message($content, $label = '')
  {
    if ($label) {
      return "<p><strong>$label:</strong> $content</p>";
    } else {
      return "<p>$content</p>";
    }
  }

  public function send()
  {
    $body = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      foreach ($_POST as $key => $value) {
        if ($key !== 'subject' && $key !== 'email' && $key !== 'name') {
          if (is_array($value)) {
            $value = implode(', ', $value);
          }
          $body .= $this->add_message($value, $key);
        }
      }

      $body .= $this->add_message('', 'Sent from PHP Email Form');

      $this->smtp_send($body);
    } else {
      return 'Method not allowed!';
    }
  }

  private function smtp_send($body)
  {
    if (!empty($this->smtp['host']) && !empty($this->smtp['username']) && !empty($this->smtp['password']) && !empty($this->smtp['port'])) {
      $headers = array();
      $headers[] = "MIME-Version: 1.0";
      $headers[] = "Content-type: text/html; charset=UTF-8";
      $headers[] = "From: {$this->from_name} <{$this->from_email}>";
      $headers[] = "Subject: {$this->subject}";
      $headers[] = "X-Mailer: PHP/" . phpversion();

      ini_set('SMTP', $this->smtp['host']);
      ini_set('smtp_port', $this->smtp['port']);
      ini_set('sendmail_from', $this->from_email);

      if (mail($this->to, $this->subject, $body, implode("\r\n", $headers))) {
        echo 'success';
      } else {
        echo 'SMTP error';
      }
    } else {
      echo 'SMTP credentials are not set!';
    }
  }
}
?>
