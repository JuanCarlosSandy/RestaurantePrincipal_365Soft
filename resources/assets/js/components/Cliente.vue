<template>
            <main class="main">
            <!-- Breadcrumb -->
            <ol class="breadcrumb">
                <!--<li class="breadcrumb-item"><a href="/">Ventas</a></li>-->
            </ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> CLIENTES
                        <button type="button" @click="abrirModal('persona','registrar')" class="btn btn-secondary">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                        <button type="button" @click="cargarReporteExcel()" class="btn btn-info">
                            <i class="icon-doc"></i>&nbsp;Reporte
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select class="form-control col-md-3" v-model="criterio">
                                      <option value="nombre">Nombre</option>
                                      <option value="num_documento">Documento</option>
                                    </select>
                                    <input type="text" v-model="buscar" @keyup="listarPersona(1,buscar,criterio)" class="form-control" placeholder="Texto a buscar">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">

                        <table class="table table-bordered table-striped table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Opciones</th>
                                    <th>Nombre</th>
                                    <th>Tipo Documento</th>
                                    <th>Documento</th>
                                    <th>Email</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="persona in arrayPersona" :key="persona.id">
                                    <td>
                                        <button type="button" @click="abrirModal('persona','actualizar',persona)" class="btn btn-warning btn-sm">
                                          <i class="icon-pencil"></i>
                                        </button>&nbsp;
                                        <template v-if="persona.estadoCli">
                                        <button type="button" class="btn btn-danger btn-sm"
                                            @click="desactivarUsuario(persona.id)">
                                            <i class="icon-trash"></i>
                                        </button>
                                        </template>
                                        <template v-else>
                                            <button type="button" class="btn btn-info btn-sm"
                                                @click="activarUsuario(persona.id)">
                                                <i class="icon-check"></i>
                                            </button>
                                        </template>
                                    </td>
                                    <td v-text="persona.nombre"></td>
                                    <td v-text="getTipoDocumentoText(persona.tipo_documento)"></td>
                                    <td v-text="persona.num_documento"></td>
                                    <td v-text="persona.email"></td>
                                    <td>
                                        <div v-if="persona.estadoCli">
                                            <span class="badge badge-success">Activo</span>
                                        </div>
                                        <div v-else>
                                            <span class="badge badge-danger">Desactivado</span>
                                        </div>
                                    </td>
                                </tr>                                
                            </tbody>
                        </table>
                    </div>

                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="pagination.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar,criterio)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar,criterio)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar,criterio)">Sig</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>
            <!--Inicio del modal agregar/actualizar-->
            <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModal"></h4>
                            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="form-group row">
                                    <div class="col-md-6">
                                    
                                            <label class="form-control-label" for="nombre"><strong>Nombre del cliente<span class="obligatorio">(Obligatorio)</span></strong></label>    

                                            <input type="text" v-model="nombre" class="form-control" id="nombre" placeholder="Ej. Juan Pérez">                                        
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-control-label" for="email"><strong>Correo Electronico</strong></label>    
                                            <input type="text" v-model="email" class="form-control" id="email" placeholder="Ej. ejemplo@dominio.com">                                        
                                    </div>
                                    <!--<div class="col-md-6">
                                        <label for="direccion" class="font-weight-bold">Dirección</label>
                                            <input type="text" v-model="direccion" class="form-control" id="direccion" placeholder="Ej. Calle Principal #123, Ciudad">                                        
                                    </div>-->
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label class="form-control-label" for="tipo_documento"><strong>Tipo de Documento<span class="obligatorio">(Obligatorio)</span></strong></label>    
                                            <div>
                                                <select class="form-control" v-model="tipo_documento">
                                                    <option value="" disabled>Selecciona una tipo de documento</option>
                                                    <option value="1">CI - CEDULA DE IDENTIDAD</option>
                                                    <option value="2">CEX - CEDULA DE IDENTIDAD DE EXTRANJERO</option>
                                                    <option value="5">NIT - NÚMERO DE IDENTIFICACIÓN TRIBUTARIA</option>
                                                    <option value="3">PAS - PASAPORTE</option>
                                                    <option value="4">OD - OTRO DOCUMENTO DE IDENTIDAD</option>   
                                                </select>                                    
                                            </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-control-label" for="num_documento"><strong>N° Documento<span class="obligatorio">(Obligatorio)</span></strong></label>    
                                            <input type="text" v-model="num_documento" class="form-control" id="num_documento" placeholder="Ej. 12345678">                                        
                                    </div>
                                </div>
                                <div v-show="errorPersona" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in errorMostrarMsjPersona" :key="error" v-text="error">

                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                            <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarPersona()">Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarPersona()">Actualizar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Fin del modal-->
        </main>
</template>

<script>
    export default {
        data (){
            return {
                persona_id: 0,
                nombre : '',
                tipo_documento : 'DNI',
                num_documento : '',
                direccion : '',
                telefono : '',
                email : '',
                arrayPersona : [],
                modal : 0,
                tituloModal : '',
                tipoAccion : 0,
                errorPersona : 0,
                errorMostrarMsjPersona : [],
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,
                criterio : 'nombre',
                buscar : ''
            }
        },
        computed:{
            isActived: function(){
                return this.pagination.current_page;
            },
            //Calcula los elementos de la paginación
            pagesNumber: function() {
                if(!this.pagination.to) {
                    return [];
                }
                
                var from = this.pagination.current_page - this.offset; 
                if(from < 1) {
                    from = 1;
                }

                var to = from + (this.offset * 2); 
                if(to >= this.pagination.last_page){
                    to = this.pagination.last_page;
                }  

                var pagesArray = [];
                while(from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;             

            }
        },
        methods : {
            listarPersona (page,buscar,criterio){
                let me=this;
                var url= '/cliente?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayPersona = respuesta.personas.data;
                    me.pagination= respuesta.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            cambiarPagina(page,buscar,criterio){
                let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarPersona(page,buscar,criterio);
            },
            registrarPersona(){
                if (this.validarPersona()){
                    return;
                }
                
                let me = this;

                axios.post('/cliente/registrar2',{
                    'nombre': this.nombre,
                    'direccion': this.direccion,
                    'tipo_documento': this.tipo_documento,
                    'num_documento': this.num_documento,
                    'telefono' : this.telefono,
                    'email': this.email
                }).then(function (response) {
                    swal(
                    'REGISTRO ÉXITOSO',
                    'Cliente Añadido',
                    'success'
                    );
                    me.cerrarModal();
                    me.listarPersona(1,'','nombre');
                }).catch(function (error) {
                    swal(
                    'REGISTRO FALLIDO',
                    'Intente de Nuevo',
                    'warning'
                );
                    console.log(error);
                });
            },
            actualizarPersona(){
               if (this.validarPersona()){
                    return;
                }
                
                let me = this;

                axios.put('/cliente/actualizar',{
                    'nombre': this.nombre,
                    'direccion': this.direccion,
                    'tipo_documento': this.tipo_documento,
                    'num_documento': this.num_documento,
                    'telefono' : this.telefono,
                    'email': this.email,
                    'id': this.persona_id
                }).then(function (response) {
                    swal(
                    'ACTUALIZACIÓN ÉXITOSA',
                    'Cliente Actualizado',
                    'success'
                    );
                    me.cerrarModal();
                    me.listarPersona(1,'','nombre');
                }).catch(function (error) {
                    swal(
                    'ACTUALIZACIÓN FALLIDA',
                    'Intente de Nuevo',
                    'warning'
                );
                    console.log(error);
                }); 
            },            
            validarPersona(){
                this.errorPersona=0;
                this.errorMostrarMsjPersona =[];

                if (!this.nombre) this.errorMostrarMsjPersona.push("El nombre de la persona no puede estar vacío.");
                if (!this.num_documento) this.errorMostrarMsjPersona.push("El número de documento de la persona no puede estar vacío.");
                if (!this.tipo_documento) this.errorMostrarMsjPersona.push("El tipo del documento de la persona no puede estar vacío.");


                if (this.errorMostrarMsjPersona.length) this.errorPersona = 1;

                return this.errorPersona;
            },
            cerrarModal(){
                this.modal=0;
                this.tituloModal='';
                this.nombre='';
                this.tipo_documento='';
                this.num_documento='';
                this.direccion='';
                this.telefono='';
                this.email='';
                this.errorPersona=0;

            },
            abrirModal(modelo, accion, data = []){
                switch(modelo){
                    case "persona":
                    {
                        switch(accion){
                            case 'registrar':
                            {
                                this.modal = 1;
                                this.tituloModal = 'Registrar Cliente';
                                this.nombre= '';
                                this.tipo_documento='';
                                this.num_documento='';
                                this.direccion='';
                                this.telefono='';
                                this.email='';
                                this.tipoAccion = 1;
                                break;
                            }
                            case 'actualizar':
                            {
                                //console.log(data);
                                this.modal=1;
                                this.tituloModal='Actualizar Cliente';
                                this.tipoAccion=2;
                                this.persona_id=data['id'];
                                this.nombre = data['nombre'];
                                this.direccion = data['direccion'];
                                this.tipo_documento = data['tipo_documento'];
                                this.num_documento = data['num_documento'];
                                this.telefono = data['telefono'];
                                this.email = data['email'];
                                break;
                            }
                        }
                    }
                }
            },
            cargarReporteExcel()
            {
                window.open('/cliente/listarReporteClienteExcel', '_blank');
            },
            desactivarUsuario(id) {
            swal({
                title: 'Esta seguro de desactivar este usuario?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar!',
                cancelButtonText: 'Cancelar',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    let me = this;

                    axios.put('/cliente/desactivarCli', {
                        'id': id
                    }).then(function (response) {
                        me.listarPersona(1, '', 'nombre');
                        swal(
                            'Desactivado!',
                            'El registro ha sido desactivado con éxito.',
                            'success'
                        )
                    }).catch(function (error) {
                        console.log(error);
                    });


                } else if (
                    // Read more about handling dismissals
                    result.dismiss === swal.DismissReason.cancel
                ) {

                }
            })
        },
        getTipoDocumentoText(value){
            switch (value) {
                case '1':
                    return 'CI';
                case '2':
                    return 'CEX';
                case '3':
                    return 'PAS';
                case '4':
                    return 'OD';
                case '5':
                    return 'NIT';    
                default:
                    return '';
            }
        },
        activarUsuario(id) {
            swal({
                title: 'Esta seguro de activar este usuario?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar!',
                cancelButtonText: 'Cancelar',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    let me = this;

                    axios.put('/cliente/activarCli', {
                        'id': id
                    }).then(function (response) {
                        me.listarPersona(1, '', 'nombre');
                        swal(
                            'Activado!',
                            'El registro ha sido activado con éxito.',
                            'success'
                        )
                    }).catch(function (error) {
                        console.log(error);
                    });


                } else if (
                    // Read more about handling dismissals
                    result.dismiss === swal.DismissReason.cancel
                ) {

                }
            })
        },
        },
        mounted() {
            this.listarPersona(1,this.buscar,this.criterio);
        }
    }
</script>
<style>    
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .mostrar{
        display: list-item !important;
        opacity: 1 !important;
        position: absolute !important;
        background-color: #3c29297a !important;
    }
    .div-error{
        display: flex;
        justify-content: center;
    }
    .text-error{
        color: red !important;
        font-weight: bold;
    }
    .table-responsive {
    margin: 20px 0;
    }

    .table-hover tbody tr:hover {
    background-color: #f1f1f1;
    }

    .btn-sm {
    padding: 0.25rem 0.5rem;
    }

    .thead-dark th {
    background-color: #343a40;
    color: white;
    }

    .table-bordered th,
    .table-bordered td {
    border: 1px solid #dee2e6;
    }

    .table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, 0.05);
    }
    .obligatorio {
    color: red;
    }
</style>
