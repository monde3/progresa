<!--MODAL PARA MOSTRAR EL TRASCURSO DE LA TÉCNICA DEL POMODORO-->
<!--Comentamos el fade para que vaya más rápido en la máquina virtual-->
<!--<div class="modal fade" tabindex="-1" role="dialog" id="modal_pomodoro">-->
<div class="modal" tabindex="-1" role="dialog" id="modal_pomodoro">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title text-center">{{ trans('adminlte_lang::message.timemanaging') }}</h3>
      </div>
      <div class="modal-body">
        <h3 id="h1-fase" align="center">{{ trans('adminlte_lang::message.timer')}}</h3>
        <div id="timer">
            <div class="container" id="cronometro-pomodoro">
                <div id="hour">00</div>
                <div class="divider">:</div>
                <div id="minute">00</div>
                <div class="divider">:</div>
                <div id="second">00</div>                
            </div>
            <button id="btn-comenzar">{{ trans('adminlte_lang::message.start')}}</button>
        </div>

      </div>
      <div class="modal-footer">
        <input id="btn-cerrar" type="button" class="btn btn-primary" data-dismiss="modal" value="{{ trans('adminlte_lang::message.close') }}">
      </div>
    </div>
  </div>
</div>

@section('scripts')
  @parent
  
  <script>

      $(document).ready(function(){
          // FASES //
          const POMODORO = 0;
          const PRE_DESCANSO = 1;
          const DESCANSO = 2;
          const PRE_POMODORO = 3;

          const TIEMPO_POMODORO = 15;//00; // 25 minutos
          const TIEMPO_DESCANSO = 3;//00; // 5 minutos
          const TIEMPO_INTERACCION = 30;

          var tiempo = {
              hora: 0,
              minuto: 0,
              segundo: 0,
              fase: -1 // Múltiplo de la fase en la que estamos
          };

          // Booleano para saber si estamos en el intervalo
          // en el que el usuario ha de interactuar con el sistema
          var interaccion = 0;

          var tiempo_corriendo = null;
          var audio_ding = new Audio('{{ asset('sounds/ding.mp3') }}');
          var audio_alarm = new Audio('{{ asset('sounds/alarm.mp3') }}');

          var titulo = $("#h1-fase");
          var boton = $("#btn-comenzar");

          var fase_actual = 0;

          $("btn-comenzar").text('{{ trans('adminlte_lang::message.start')}}');

          $("#btn-comenzar").click(function(){
              //COMENZAR
              if( tiempo.fase == -1 )
              {
                  gestion_intervalo();
                  audio_alarm.pause();
                  audio_alarm.currentTime = 0;
                  $(this).text('{{ trans('adminlte_lang::message.stop')}}');                      
                  titulo.text('{{ trans('adminlte_lang::message.work')}}');  
                  tiempo_corriendo = setInterval(intervalo, 1000);               
                  tiempo.fase++;
              }
              //DESCANSAR
              else if((tiempo.fase % 4) == PRE_DESCANSO)
              {
                  gestion_intervalo();
                  reset_clock();
                  $(this).text('{{ trans('adminlte_lang::message.stop')}}');
                  titulo.text('{{ trans('adminlte_lang::message.rest')}}');
                  $("#cronometro-pomodoro").css('background', 'gray');
                  tiempo.fase++;
                  audio_ding.play();
              }
              //POMODORO
              else if((tiempo.fase % 4) == PRE_POMODORO)
              {
                  gestion_intervalo();
                  reset_clock();
                  $(this).text('{{ trans('adminlte_lang::message.stop')}}'); 
                  titulo.text('{{ trans('adminlte_lang::message.work')}}');  
                  $("#cronometro-pomodoro").css('background', 'gray');                   
                  tiempo.fase++;
                  audio_ding.play();
              }
              //DETENER
              else 
              {
                  $(this).text('{{ trans('adminlte_lang::message.start')}}');
                  reset_all();
              }
          })

          //Capturamos el evento que cierra el modal
          $('#modal_pomodoro').on('hidden.bs.modal', function(){ 
              // Refrescamos la página para recargar 
              // los parámetros de la tarea (estado y nuevos tiempos)
              if(fase_actual == 1){
                  gestion_intervalo();
              }
              location.reload();
          })

          function gestion_intervalo(){
              var alumno_tarea_id = $("#alumno_tarea_id").text().trim();
              var url_tarea = "{{ url ('gestionPomodoro') }}"
                                .concat("/").concat(alumno_tarea_id)
                                .concat("/").concat(fase_actual);

              $.ajax({
                  type: "GET",
                  url: url_tarea
              }).done(function t(response) {
                  var resp = response.split("/");
                  if(resp[0]=='OK'){
                    if (fase_actual == 1){
                      fase_actual = 0;
                      $("#header-oro").text(resp[1]);
                      $("#header-exp").text(resp[2]);
                    }
                    else{
                      fase_actual = 1;
                      $("#header-vida").text(resp[1]);
                    }
                  }
                  else{
                    //Ha habido algún problema
                    reset_all();
                    $("#modal_pomodoro").modal('toggle');
                    $("#modal_mensaje_titulo").text("{{ trans('adminlte_lang::message.warning') }}");
                    $("#modal_mensaje_texto").text(resp[1]);
                    $("#modal_mensaje").show();
                  }

                  
              });
          };

          function update_clock(){
              $("#hour").text(tiempo.hora < 10 ? '0' + tiempo.hora : tiempo.hora);
              $("#minute").text(tiempo.minuto < 10 ? '0' + tiempo.minuto : tiempo.minuto);
              $("#second").text(tiempo.segundo < 10 ? '0' + tiempo.segundo : tiempo.segundo);
          };

          function reset_clock(){
              tiempo.hora = 0;
              tiempo.minuto = 0;
              tiempo.segundo = 0;

              update_clock();
          };

          function reset_all(){
              $("#cronometro-pomodoro").css('background', 'gray');
              titulo.text('{{ trans('adminlte_lang::message.timer') }}');
               
              reset_clock();
              tiempo.fase = -1;
              interaccion = 0;

              audio_alarm.pause();
              audio_alarm.currentTime = 0;

              clearInterval(tiempo_corriendo);
          };

          function time_out(){
              titulo.text('Tiempo agotado');
              $("#cronometro-pomodoro").css('background', 'gray');
              boton.text('{{ trans('adminlte_lang::message.start')}}');

              reset_clock();
              tiempo.fase = -1;
              interaccion = 0;

              audio_alarm.play();

              clearInterval(tiempo_corriendo);
          };

          function phase_over(button_text){
              boton.text(button_text);
              titulo.text(button_text);
              reset_clock();
              tiempo.fase++;
              audio_ding.play();
          };

          // Función que se ejecutará cada segundo del intervalo
          // comprobaremos cada segundo en qué situación estamos
          function intervalo(){
              // Segundos
              tiempo.segundo++;
              if(tiempo.segundo >= 60)
              {
                  tiempo.segundo = 0;
                  tiempo.minuto++;
              }      

              // Minutos
              if(tiempo.minuto >= 60)
              {
                  tiempo.minuto = 0;
                  tiempo.hora++;
              }

              if(tiempo.fase % 4 == POMODORO && tiempo.segundo == TIEMPO_POMODORO)
              {
                  phase_over('{{ trans('adminlte_lang::message.rest')}}');
              }
              else if(tiempo.fase % 4 == PRE_DESCANSO && tiempo.segundo == TIEMPO_INTERACCION)
              {
                  time_out();
              }
              else if(tiempo.fase % 4 == DESCANSO && tiempo.segundo == TIEMPO_DESCANSO)
              {
                  phase_over('{{ trans('adminlte_lang::message.work')}}');
              }
              else if(tiempo.fase % 4 == PRE_POMODORO && tiempo.segundo == TIEMPO_INTERACCION)
              {
                  time_out();
              }

              update_clock();

              // Estamos en uno de los dos intervalos de interacción
              if( tiempo.fase % 2 == 1 )
              {
                  if(tiempo.segundo % 2 == 0)
                      $("#cronometro-pomodoro").css('background', 'red');
                  else
                      $("#cronometro-pomodoro").css('background', 'gray');
              }

          };

      })
  </script>

@endsection