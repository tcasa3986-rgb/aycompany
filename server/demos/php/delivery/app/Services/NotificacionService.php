<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificacionService
{
    /**
     * Notifica un cambio de estado al cliente vía email.
     * Devuelve el link wa.me listo para que un operador lo envíe por WhatsApp.
     */
    public function cambioEstadoPedido(Pedido $pedido): array
    {
        $pedido->loadMissing('cliente');
        $cliente = $pedido->cliente;
        $mensaje = $this->construirMensaje($pedido);

        // Email — solo si el cliente tiene email y la configuración lo permite
        if ($cliente && $cliente->email && Configuracion::obtener('notif_email', '1') == '1') {
            try {
                Mail::raw($mensaje, function ($m) use ($cliente, $pedido) {
                    $m->to($cliente->email)
                      ->subject("Pedido {$pedido->numero} — " . ucfirst(str_replace('_',' ', $pedido->estado)));
                });
            } catch (\Throwable $e) {
                Log::warning("Error enviando mail: ".$e->getMessage());
            }
        }

        return [
            'mensaje'  => $mensaje,
            'whatsapp' => $this->linkWhatsapp($cliente?->telefono, $mensaje),
        ];
    }

    /**
     * Mensaje plantilla por estado.
     */
    public function construirMensaje(Pedido $pedido): string
    {
        $empresa = Configuracion::obtener('empresa_nombre', 'Delivery');
        $nombre  = $pedido->cliente->nombre ?? 'estimado cliente';

        return match ($pedido->estado) {
            'confirmado' => "Hola {$nombre}, tu pedido *{$pedido->numero}* fue *confirmado*. Total: S/ ".number_format($pedido->total,2).". ¡Lo estamos preparando! — {$empresa}",
            'preparando' => "Hola {$nombre}, estamos preparando tu pedido *{$pedido->numero}*. Pronto saldrá hacia ti. — {$empresa}",
            'listo'      => "Tu pedido *{$pedido->numero}* está listo y será asignado al repartidor. — {$empresa}",
            'en_camino'  => "¡Tu pedido *{$pedido->numero}* va en camino! 🛵 ".($pedido->repartidor ? "Repartidor: {$pedido->repartidor->nombre}." : "").
                             ($pedido->repartidor && $pedido->repartidor->telefono ? " Tel: {$pedido->repartidor->telefono}." : "")." — {$empresa}",
            'entregado'  => "¡Pedido *{$pedido->numero}* entregado! Gracias por tu compra, {$nombre}. Cuéntanos qué tal tu experiencia. — {$empresa}",
            'cancelado'  => "Tu pedido *{$pedido->numero}* fue cancelado. ".($pedido->motivo_cancelacion ? "Motivo: {$pedido->motivo_cancelacion}." : "")." — {$empresa}",
            default      => "Tu pedido *{$pedido->numero}* tiene un nuevo estado: ".ucfirst($pedido->estado).". — {$empresa}",
        };
    }

    /**
     * Construye link wa.me con el mensaje urlencodeado.
     */
    public function linkWhatsapp(?string $telefono, string $mensaje): ?string
    {
        if (!$telefono) return null;
        $tel = preg_replace('/\D/', '', $telefono);
        if (strlen($tel) < 9) return null;
        // Si vino sin código de país, asumimos Perú (+51)
        if (strlen($tel) <= 9) $tel = '51' . $tel;
        return 'https://wa.me/' . $tel . '?text=' . rawurlencode($mensaje);
    }
}
