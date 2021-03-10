<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mailmessages;
use Illuminate\Support\Facades\Mail;


class Emailnotaryusers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailing:notaryusers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Este command envia los correos electronicos de los registros en la tabla portal.mail_messages donde el estatus es 0, cuando intenta enviar y no puede el estatus cambia a 99, finalmente cuando la notificacion es enviada se marca con estatus 1';


    /**
     * Create a new command instance.
     *
     * @return void
     */

    protected $messages;
    protected $maxTries = 5;
    public function __construct()
    {
        parent::__construct();
        $this->messages = new Mailmessages();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // obtener las listas pendientes de correo
        $pending = $this->pendientesEnvio();

        if($pending != 0)
        {
            $proceso = $this->procesarEnvio($pending);

            // final del proceso
            $fin = $this->updateAnswers($proceso, $pending);

        }else{
            dd("No hay envios pendientes");
        }
    
    }


    /**
     * Este metodo regresa todos los archivos de correo pendientes para enviar. 
     * (sent = 0)
     * 
     * @param null 
     *
     * @return object
     */    

    private function pendientesEnvio()
    {
        $p = $this->messages->whereIn("sent", [0, 99])->where(function($q) {
            $q->where('tries', null)
            ->orWhere('tries', '<', $this->maxTries);
        })->get();
        
        if($p->count() > 0)
        {
            $data = array();
            
            foreach($p as $info)
            {
                $data[]= array(
                    "id"        => $info->id,
                    "to"        => $info->user,
                    "message"   => $info->message,
                    "logs"      => $info->logs,
                    "tries"     => $info->tries
                );
            }

            return $data;

        }else{
            return 0;
        }
    }

    /**
     * Envia los correos electronicos y regresa un arreglo con el estatus del envio. 
     * (sent = 0)
     * 
     * @param array con registros de la tabla mail_messages con sent = 0
     *
     * @return object
     */ 
    private function procesarEnvio($info)
    {
        foreach($info as $i)
        {
            $answer[$i["id"]] = $this->sendMailMessage($i["to"],$i["message"]);

        }

        return $answer;
    }

    /**
     * Este metodo es el que envia el correo electronico. 
     * (sent = 0)
     * 
     * @param array con registros de la tabla mail_messages con sent = 0
     *
     * @return 1 si se mando correctamente o 99 si no se pudo mandar
     */ 
    private function sendMailMessage($to,$data)
    {
        try{
            Mail::send([], [], function($message) use($to, $data) {
                $message->to($to);
                $message->subject('Notaria');
                $message->setBody($data, 'text/html');
            });
    
            echo "Send: {$to}\n";
            return [1];
        }catch( \Exception $e ){
            echo "Error: {$to}\n";
            return [99, $e->getMessage()];
        }
        
    
    }

    /**
     * Este actualiza la tabla cuando se intento realizar el envio. 
     * (sent = 0)
     * 
     * @param array con registros de la tabla mail_messages con sent = 0
     *
     * @return 1 si se mando correctamente o 99 si no se pudo mandar
     */ 
    private function updateAnswers($array, $actual)
    {
        foreach($array as $i => $j){
            $data = [ "sent" => $j[0] ];
            if(isset($j[1])){
                $key = array_search($i, array_column($actual, 'id'));
                $logs = $actual[$key]['logs'] ? "{$actual[$key]['logs']}|" : "";
                $data["tries"] = $actual[$key]['tries'] ? intval($actual[$key]['tries']) + 1 : 1;
                $data["logs"] = $logs.$j[1];
            }else{
                $data["tries"] = null;
                $data["logs"] = null;
            }

            $affectedRows = $this->messages->where("id", $i)->update($data);
        }
    }
}