(function(){
    const URLEditar = '/admin/eventos/editar';
    const horas = document.querySelector('#horas');
    
    if (horas) {
        const categoria = document.querySelector('[name="categoria_id"]');
        const dias = document.querySelectorAll('[name="dia"]');
        const inputHiddenDia = document.querySelector('[name="dia_id"]');
        const inputHiddenHora = document.querySelector('[name="hora_id"]');

        const URLActual = window.location.href;
        const inputDiaInicial = inputHiddenDia.value;
        const inputHoraInicial = inputHiddenHora.value;
        const inputCategoriaInicial = categoria.value;

        categoria.addEventListener('change', terminoBusqueda);
        dias.forEach(dia => dia.addEventListener('change', terminoBusqueda));

        let busqueda = {
            categoria_id: +categoria.value || '',
            dia: +inputHiddenDia.value || ''
        }

        if (!Object.values(busqueda).includes('')) {
           async function esperarAwait() {
                await buscarEventos();
                habilitarHoraInicial();
            }
            esperarAwait();
        }

        function habilitarHoraInicial() {
            const id = inputHoraInicial;
            const horaInicial = document.querySelector(`[data-hora-id="${id}"]`);
            horaInicial.classList.remove('horas__hora--deshabilitada');
            horaInicial.classList.add('horas__hora--seleccionada');
            horaInicial.onclick = seleccionarHora;
            inputHiddenHora.value = id;
        }
        
        function terminoBusqueda(e) {
            busqueda[e.target.name] = e.target.value;

            // Reiniciar los campos ocultos y el selector de horas
            inputHiddenHora.value = '';
            inputHiddenDia.value = '';

            const horaPrevia = document.querySelector('.horas__hora--seleccionada');

            if (horaPrevia) {
                horaPrevia.classList.remove('horas__hora--seleccionada');
            }

            // Continua ejecutando el codigo solo cuando esten los 2 valores llenos
            if (Object.values(busqueda).includes('')) {
                return;
            }

            buscarEventos();
        }
        
        async function buscarEventos() {
            const {dia, categoria_id} = busqueda;
            const url = `/api/eventos-horario?dia_id=${dia}&categoria_id=${categoria_id}`; 

            const resultado = await fetch(url);
            const eventos = await resultado.json(); 
            obtenerHorasDisponibles(eventos);
        }

        function obtenerHorasDisponibles(eventos) {
            // Reiniciar las horas
            const listadoHoras = document.querySelectorAll('#horas li');
            listadoHoras.forEach(li => {        
                li.classList.remove('horas__hora--seleccionada'); // Removemos la clase para que al momento de cambiar la categoria/dia no siga seleccionada la hora inicial                
                
                li.classList.add('horas__hora--deshabilitada');
            });

            // Comprobar eventos ya tomados, y quitar la variable de deshabilitado
            const horasTomadas = eventos.map(evento => evento.hora_id);
            const listadoHorasArray = Array.from(listadoHoras);

            const resultado = listadoHorasArray.filter(li => !horasTomadas.includes(li.dataset.horaId));
            resultado.forEach(li => li.classList.remove('horas__hora--deshabilitada'));

            if (URLActual.includes(URLEditar)) { // Ejecutamos esta parte del codigo solo si estamos en la url de admin/eventos/editar
                
                if (busqueda.categoria_id == inputCategoriaInicial && busqueda.dia == inputDiaInicial) {
                    habilitarHoraInicial();
                }

            }

            const horasDisponibles = document.querySelectorAll('#horas li:not(.horas__hora--deshabilitada)');
            horasDisponibles.forEach((hora) => hora.addEventListener('click', seleccionarHora));

            const horasDeshabilitadas = document.querySelectorAll('.horas__hora--deshabilitada');
            horasDeshabilitadas.forEach(hora => hora.removeEventListener('click', seleccionarHora)); // Removemos los eventListener a las horas deshabilitadas
        }

        function seleccionarHora(e) {

            // Deshabilitar la hora previa, si hay un nuevo click
            const horaPrevia = document.querySelector('.horas__hora--seleccionada');
            if (horaPrevia) {
                horaPrevia.classList.remove('horas__hora--seleccionada');
            }

            // Agregar clase de seleccionado
            e.target.classList.add('horas__hora--seleccionada');
            // Llenar el campo oculto de hora
            inputHiddenHora.value = e.target.dataset.horaId;
            // Llenar el campo oculto de dia
            inputHiddenDia.value = document.querySelector('[name="dia"]:checked').value;
        }
    }
})();