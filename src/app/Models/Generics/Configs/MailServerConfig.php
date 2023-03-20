<?php
namespace ArtisanBR\Adminx\Common\App\Models\Generics\Configs;

use Exception;
use Illuminate\Support\Facades\Crypt;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use ArtisanLabs\GModel\GenericModel;

class MailServerConfig extends GenericModel
{

    protected $fillable = [
        'host',
        'port',
        'user',
        'from_address',
        'from_name',
        'password',
        'security',
        'checked',
    ];

    protected $attributes = [
        'checked' => false,
        'port' => 465,
        'security' => 'ssl',
    ];

    protected $casts = [
        'password' => 'string',
        'user' => 'string',
        'checked' => 'bool',
    ];

    public function checkConnection(){
        if(!empty($this->password ?? null)) {
            try {
                $mail = new PHPMailer(true);

                //Server settings
                $mail->SMTPDebug = false;                      //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host = $this->host;                     //Set the SMTP server to send through
                $mail->SMTPAuth = true;                                   //Enable SMTP authentication
                $mail->Username = $this->user;                     //SMTP username
                $mail->Password = $this->password_decrypt();                               //SMTP password
                $mail->SMTPSecure = !empty($this->security) ? $this->security : null;            //Enable implicit TLS encryption
                $mail->Port = $this->port;

                $this->checked = $mail->smtpConnect();

            } catch (Exception $e) {
                $this->checked = false;
            }
        }else{
            $this->checked = false;
        }

        return $this->checked;
    }

    public function lockPassword(): ?string
    {
        if(!empty($this->password ?? null)) {
            $this->password = Crypt::encrypt($this->password);
        }
        return $this->password;
    }

    public function password_decrypt(): string
    {
        return !empty($this->password ?? null) ? Crypt::decrypt($this->password) : '';
    }

    //region Attributes
    //region Gets
    protected function getFromAddressAttribute(){
        return $this->attributes['from_address'] ?? $this->attributes['user'] ?? null;
    }

    /*protected function getHostAttribute(){
        return $this->attributes['host'] ?? config('mail.mailers.smtp.host');
    }
    protected function getPortAttribute(){
        return $this->attributes['host'] ?? config('mail.mailers.smtp.port');
    }
    protected function getUserAttribute(){
        return $this->attributes['user'] ?? config('mail.mailers.smtp.username');
    }
    protected function getPasswordAttribute(){
        return $this->attributes['password'] ?? config('mail.mailers.smtp.password');
    }*/
    //endregion
    //endregion
}
