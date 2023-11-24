<?php
if (! function_exists('update_hosting')) {
    function update_hosting() {
        $current_date = date('Y-m-d');
        $current_time = date('H:i');
        // Verificar que sean más de las 12 del medio día
        if($current_time > setting('system.update_hosting') ?? '12:00'){
            // Obtener todas las habitaciones "ocupadas"
            $reservation_details = App\Models\ReservationDetail::with(['days', 'accessories' => function($q){
                $q->where('status', 1);
            }])->where('status', 'ocupada')->get();
            foreach ($reservation_details as $item) {
                if($item->days->count()){
                    // Obtener el último registro de hospedaje
                    $last_resgister = $item->days->sortByDesc('date')->first();
                    // Si el último registro de hospedaje fue del día anterior
                    if($last_resgister->date < $current_date){
                        $date_register = date('Y-m-d', strtotime($last_resgister->date.' +1 days'));
                        while ($date_register <= $current_date) {
                            App\Models\ReservationDetailDay::create([
                                'reservation_detail_id' => $item->id,
                                'date' => $date_register,
                                'amount' => $item->price + $item->accessories->sum('price')
                            ]);
                            $date_register = date('Y-m-d', strtotime($date_register.' +1 days'));
                        }
                    }
                }
            }
        }
    }
}