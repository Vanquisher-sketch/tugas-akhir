<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class DataModificationNotification extends Notification
{
    use Queueable;

    protected $modifier; // User yang melakukan aksi
    protected $action;   // Aksi (ditambahkan, diperbarui, dihapus)
    protected $modelName;// Nama model (Tanah, Inventaris)
    protected $itemName; // Nama item data

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $modifier, $action, $modelName, $itemName)
    {
        $this->modifier = $modifier;
        $this->action = $action;
        $this->modelName = $modelName;
        $this->itemName = $itemName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database']; // Menyimpan notifikasi ke database
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        // Data yang akan disimpan di kolom 'data' pada tabel notifications
        return [
            'modifier_name' => $this->modifier->name,
            'action' => $this->action,
            'model_name' => $this->modelName,
            'item_name' => $this->itemName,
            'message' => "Data {$this->modelName} '{$this->itemName}' telah {$this->action} oleh {$this->modifier->name}.",
        ];
    }
}
