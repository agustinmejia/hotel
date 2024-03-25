const language = {
    sProcessing: "Procesando...",
    sLengthMenu: "Mostrar _MENU_ registros",
    sZeroRecords: "No se encontraron resultados",
    sEmptyTable: "Ningún dato disponible en esta tabla",
    sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
    sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
    sSearch: "Buscar:",
    sInfoThousands: ",",
    sLoadingRecords: "Cargando...",
    oPaginate: {
        sFirst: "Primero",
        sLast: "Último",
        sNext: "Siguiente",
        sPrevious: "Anterior"
    },
    oAria: {
        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
    },
    buttons: {
        copy: "Copiar",
        colvis: "Visibilidad"
    }
}

function customDataTable(url, columns = [], order = 0, orderBy = 'desc'){
    $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        searchDelay : 1000,
        ajax: url,
        columns,
        order: [[ order, orderBy ]],
        language
    });
}

function customSelect(select, url, templateResult, templateSelection, dropdownParent, btnNoResults){
    $(select).select2({
        dropdownParent: dropdownParent ? dropdownParent : null,
        language: {
            noResults: function() {
                return btnNoResults ? `Resultados no encontrados <button type="button" class="btn btn-link" onclick="${btnNoResults}">Crear nuevo <i class="fa fa-plus"></i></button>` : 'Resultados no encontrados';
            },
            inputTooShort: function (data) {
                return `Ingrese ${data.minimum - data.input.length} o más caracteres`;
            },
        },
        escapeMarkup: function(markup) {
            return markup;
        },
        ajax: { 
            allowClear: true,
            url,
            type: "get",
            dataType: 'json',
            delay: 500,
            processResults: function (response) {
                return {
                    results: response
                };
            }
        },
        minimumInputLength: 4,
        templateResult,
        templateSelection
    });
}

function formatResultPeople(data) {
    if (data.loading) {
        return 'Buscando...';
    }
    let image = "/images/default.jpg";
    if(data.image){
        image = "/storage/"+data.photo.replace('.', '-cropped.');
    }
    var $container = $(
        `<div class="option-select2-custom">
            <div style="display:flex; flex-direction: row">
                <div>
                    <img src="${image}" style="width: 60px; height: 60px; border-radius: 30px; margin-right: 10px" />
                </div>
                <div>
                    <h5>
                        ${data.full_name} ${data.defaulters.length ? '<label class="label label-danger" >Deudor</label>' : ''}<br>
                        <p style="font-size: 13px; margin-top: 5px">
                            CI: ${data.dni ? data.dni : 'No definido'}
                        </p>
                    </h5>
                </div>
            </div>
            
        </div>`
    );

    return $container;
}

function formatResultCities(data) {
    if (data.loading) {
        return 'Buscando...';
    }
    var $container = $(
        `<div class="option-select2-custom">
            <div style="display:flex; flex-direction: row">
                <div>
                    <h5>
                        ${data.name}<br>
                        <p style="font-size: 13px; margin-top: 5px">
                            ${data.state ? data.state.name : 'No definido'} | ${data.state ? data.state.country ?  data.state.country.name : 'No definido' : 'No definido'}
                        </p>
                    </h5>
                </div>
            </div>
            
        </div>`
    );

    return $container;
}

function formatResultProducts(data) {
    if (data.loading) {
        return 'Buscando...';
    }
    let image = "/images/default.jpg";
    if(data.images){
        image = "/storage/"+data.images.replace('.', '-cropped.');
    }
    var $container = $(
        `<div class="option-select2-custom">
            <div style="display:flex; flex-direction: row">
                <div>
                    <img src="${image}" style="width: 60px; height: 60px; border-radius: 30px; margin-right: 10px" />
                </div>
                <div>
                    <h5>
                        ${data.name} <br>
                        <p style="font-size: 13px; margin-top: 5px; font-weight: bold">
                            ${data.price} Bs. | ${data.type.name} (${parseInt(data.stock[0].quantity)} Unids.)
                        </p>
                    </h5>
                </div>
            </div>
            
        </div>`
    );

    return $container;
}

$(document).ready(function(){
    $('#form-person').submit(function(e){
        e.preventDefault();
        $.post($(this).attr('action'), $(this).serialize(), function(res){
            if (res.success) {
                toastr.success('Huesped registrado', 'Bien hecho');
                $('.form-submit .btn-submit').removeAttr('disabled');
                $('#form-person').trigger('reset');
                $('#person-modal').modal('hide');
                $('#select-city_id').val(null).trigger('change');
            } else {
                toastr.error('Ocurrió un error', 'Error');
            }
        });
    });
});