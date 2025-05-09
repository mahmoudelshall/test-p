<?php

namespace App\Helpers;

use App\Models\MailFailure;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use \Exception;
use Illuminate\Support\Facades\Log;

class MailHelper extends Mailable
{
    use Queueable, SerializesModels;

    private $from_address;
    private $to_address = [];
    private $view_content;
    private $text_content;
    private $subject_title;
    public $data;
    protected $any = "g";
    private $attach_file;
    private $attach_file_name;
    private $cc_address = [];
    private $attach_raw_data;
    public function __construct($from = null, $to = null, $view = null, $text = null, $subject = null,
                                $data = null, $attach = null, $attach_name = null, $cc = null, $attach_data = null)
    {
        $this->config($from, $to, $view, $text, $subject, $data, $attach, $attach_name, $cc, $attach_data);
    }

    public function build()
    {
        if ($this->view_content) {
            $this->view($this->view_content);
        }

        if ($this->from_address) {
            $this->from($this->from_address);
        }
        if ($this->to_address) {
            foreach ($this->to_address as $address) {
                $this->to($address);
            }
        }
        if ($this->cc) {
            foreach ($this->cc_address as $cc){
                $this->cc($cc);
            }
        }
        if ($this->subject_title) {
            $this->subject($this->subject_title);
        }
        if (is_array($this->attach_file) && count($this->attach_file) > 0) {
            foreach ($this->attach_file as $key => $item) {
                $this->attach($item, [
                    'as' => $this->attach_file_name[$key] ?? null,
                ]);
            }
        } elseif (!is_array($this->attach_file) && $this->attach_file != null) {
            $this->attach($this->attach_file, [
                'as' => $this->attach_file_name ?? null,
            ]);
        }

        if ($this->attach_raw_data){
            $this->attachData($this->attach_raw_data,$this->attach_file_name, [
                'mime' => 'application/pdf',
            ]);
        }
        return $this;
    }

    private function config($from = null, $to = null, $view = null, $text = null, $subject = null,
                            $data = null, $attach = null, $attach_name = null, $cc = null, $attach_data = null)
    {

        if ($from) {
            $this->from_address = $from;
        }

        if (!$this->from_address) {
            $this->from_address = env('MAIL_FROM_ADDRESS', 'info@pms.yallahost.io');
        }

        if ($to) {
            array_push($this->to_address,$to);
        }

        if ($cc) {
            array_push($this->cc_address ,$cc);
        }
        if (!$this->to_address) {
            throw new Exception("Yo Must Send the second parameter \$to");
        }

        if ($view) {
            $this->view_content = $view;
        }

        if (!$this->view_content) {
            $this->view_content = "mails.default";
        }

        if ($text) {
            $this->text_content = $text;
        }

        if ($subject) {
            $this->subject_title = $subject;
        }

        if ($data) {
            $this->data = $data;
        }

        if ($attach) {
            $this->attach_file = $attach;
        }

        if ($attach_name) {
            $this->attach_file_name = $attach_name;
        }
        if ($attach_data){
            $this->attach_raw_data = $attach_data;
        }

    }

    public function sending($from = null, $to = null, $view = null, $text = null, $subject = null,
                            $data = null, $attach = null, $attach_name = null, $cc = null,$attach_data = null)
    {
        try {
            $this->config($from, $to, $view, $text, $subject, $data, $attach, $attach_name, $cc, $attach_data);

            if ($cc){
                Mail::bcc($cc)->send($this);
            }
            else
                Mail::to($this->to)->send($this);
        } catch (Exception $e) {
            if (is_array($to)) {
                $to = implode(',', $to);
            }

            if (is_array($data)) {
                $data = implode(',', $data);
            }

            $error = [
                'from' => $this->from_address,
                'to' => $to,
                'attach_name' => $attach_name,
                'view' => $view,
                'text' => $text,
                'subject' => $subject,
                'data' => $data
            ];
          Log::error("Email Send fail: " . $e->getMessage());
            return false;
        }
        return $this;
    }

}


