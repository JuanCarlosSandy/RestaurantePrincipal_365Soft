<template>
            <main class="main">
            <!-- Breadcrumb -->
            <ol class="breadcrumb">
                <!--<li class="breadcrumb-item"><a href="/">Escritorio</a></li>-->
            </ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> CATEGORÍA BEBIDAS
                        <button type="button" @click="abrirModal('categoria','registrar')" class="btn btn-secondary">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select class="form-control col-md-3" v-model="criterio">
                                      <option value="nombre">Nombre</option>
                                      <option value="descripcion">Descripción</option>
                                    </select>
                                    <input type="text" v-model="buscar" @keyup="listarCategoria(1,buscar,criterio)" class="form-control" placeholder="Texto a buscar">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Opciones</th>
                                        <th>Codigo</th>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="categoria in arrayCategoria" :key="categoria.id">
                                        <td>
                                            <button type="button" @click="abrirModal('categoria','actualizar',categoria)" class="btn btn-warning btn-sm">
                                                <i class="icon-pencil"></i>
                                            </button> &nbsp;
                                            <template v-if="categoria.condicion">
                                                <button type="button" class="btn btn-danger btn-sm" @click="desactivarCategoria(categoria.id)">
                                                    <i class="icon-trash"></i>
                                                </button>
                                            </template>
                                            <template v-else>
                                                <button type="button" class="btn btn-info btn-sm" @click="activarCategoria(categoria.id)">
                                                    <i class="icon-check"></i>
                                                </button>
                                            </template>
                                        </td>
                                        <td v-text="categoria.codigo"></td>
                                        <td v-text="categoria.nombre"></td>
                                        <td v-text="categoria.descripcion ? categoria.descripcion : 'Sin descripción'"></td>
                                        <td>
                                            <div v-if="categoria.condicion">
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
                                    <label class="col-md-4 form-control-label" for="text-input"> <strong>Nombre <span class="obligatorio">(Obligatorio)</span></strong></label>                                    
                                    <div class="col-md-8">
                                        <input type="text" v-model="nombre" class="form-control" placeholder="Ingrese el nombre de la Categoria">
                                        
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 form-control-label" for="email-input"><strong>Descripción</strong></label>
                                    <div class="col-md-8">
                                        <input type="email" v-model="descripcion" class="form-control" placeholder="Ingrese una descripción (OPCIONAL)">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 form-control-label" for="email-input"><strong>Actividad Económica <span class="obligatorio">(Obligatorio)</span></strong></label>
                                    <div class="col-md-8">
                                        <select v-model="codigoActividadEconomica" class="form-control">
                                            <option value="0" disabled>Seleccione su Actividad Economica</option>
                                            <option v-for="actividadEconomica in arrayActividadEconomica" :value="actividadEconomica.codigoCaeb"
                                                v-text="actividadEconomica.descripcion"></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 form-control-label" for="email-input"><strong>Código SIAT <span class="obligatorio">(Obligatorio)</span></strong></label>
                                    <div class="col-md-8">
                                        <select v-model="codigoProductoServicio" class="form-control">
                                            <option 
                                                v-for="productoServicio in arrayProductoServicio" 
                                                :value="productoServicio.codigoProducto"
                                                :title="productoServicio.descripcionProducto">
                                                {{ productoServicio.descripcionProducto.length > 50 
                                                    ? productoServicio.descripcionProducto.substring(0, 50) + "..." 
                                                    : productoServicio.descripcionProducto 
                                                }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div v-show="errorCategoria" class="form-group row div-error">
                                    <div class="text-center text-error">
                                        <div v-for="error in errorMostrarMsjCategoria" :key="error" v-text="error">

                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                            <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="registrarCategoria()">Guardar</button>
                            <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizarCategoria()">Actualizar</button>
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
                categoria_id: 0,
                nombre : '',
                descripcion : '',
                arrayCategoria : [],
                arrayProductoServicio: [],
                arrayActividadEconomica: [],
                codigoActividadEconomica: '',
                codigoProductoServicio: '',
                modal : 0,
                tituloModal : '',
                tipoAccion : 0,
                errorCategoria : 0,
                errorMostrarMsjCategoria : [],
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
            listarCategoria (page,buscar,criterio){
                let me=this;
                var url= '/categoria?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio;
                axios.get(url).then(function (response) {
                    console.log('Categoria');
                    var respuesta= response.data;
                    me.arrayCategoria = respuesta.categorias.data;
                    me.pagination= respuesta.pagination;
                    console.log(me.arrayCategoria);
                })
                .catch(function (error) {
                    console.log(error);
                });
            },

            consultaProductosServicios() {
                let me = this;
                var url = '/categoria/consultaProductosServicios';
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayProductoServicio = respuesta.RespuestaListaProductos.listaCodigos;
                    console.log(respuesta.RespuestaListaProductos.listaCodigos);
                }).catch(function (error) {
                    console.log(error);
                });
            },

            consultaActividadEconomica() {
                let me = this;
                var url = '/categoria/consultaActividadEconomica';
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.arrayActividadEconomica = respuesta.RespuestaListaActividades.listaActividades;
                    console.log(respuesta.RespuestaListaActividades.listaActividades);
                }).catch(function (error) {
                    console.log(error);
                });
            },

            cambiarPagina(page,buscar,criterio){
                let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarCategoria(page,buscar,criterio);
            },
            registrarCategoria(){
                if (this.validarCategoria()){
                    return;
                }
                
                let me = this;

                axios.post('/categoria/registrar',{
                    'nombre': this.nombre,
                    'descripcion': this.descripcion,
                    'codigo': this.codigoProductoServicio,
                    'actividadEconomica': this.codigoActividadEconomica
                    
                }).then(function (response) {
                    swal(
                        'REGISTRO ÉXITOSO',
                        'Categoría Registrada',
                        'success'
                    );
                    me.cerrarModal();
                    me.listarCategoria(1,'','nombre');
                }).catch(function (error) {
                    console.log(error);
                    swal(
                        'REGISTRO FALLIDO',
                        'Intente de Nuevo',
                        'warning'
                    );
                });
            },
            actualizarCategoria(){
               if (this.validarCategoria()){
                    return;
                }
                
                let me = this;

                axios.put('/categoria/actualizar',{
                    'nombre': this.nombre,
                    'descripcion': this.descripcion,
                    'id': this.categoria_id,
                    'codigo': this.codigoProductoServicio,
                    'actividadEconomica': this.codigoActividadEconomica
                }).then(function (response) {
                    swal(
                        'ACTUALIZACIÓN ÉXITOSA',
                        'Categoría Actualizada',
                        'success'
                    );
                    me.cerrarModal();
                    me.listarCategoria(1,'','nombre');
                }).catch(function (error) {
                    console.log(error);
                    swal(
                        'ACTUALIZACIÓN FALLIDA',
                        'Intente de Nuevo',
                        'warning'
                    );
                }); 
            },
            desactivarCategoria(id){
               swal({
                title: 'Esta seguro de desactivar esta categoría?',
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

                    axios.put('/categoria/desactivar',{
                        'id': id
                    }).then(function (response) {
                        me.listarCategoria(1,'','nombre');
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
            activarCategoria(id){
               swal({
                title: 'Esta seguro de activar esta categoría?',
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

                    axios.put('/categoria/activar',{
                        'id': id
                    }).then(function (response) {
                        me.listarCategoria(1,'','nombre');
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
            validarCategoria() {
                this.errorCategoria = 0;
                this.errorMostrarMsjCategoria = [];

                if (!this.nombre) this.errorMostrarMsjCategoria.push("El nombre de la categoría no puede estar vacío.");
                if (!this.codigoActividadEconomica) this.errorMostrarMsjCategoria.push("El código de actividad económica no puede estar vacío.");
                if (!this.codigoProductoServicio) this.errorMostrarMsjCategoria.push("El código de producto o servicio no puede estar vacío.");

                if (this.errorMostrarMsjCategoria.length) this.errorCategoria = 1;

                return this.errorCategoria;
            },

            cerrarModal(){
                this.modal=0;
                this.tituloModal='';
                this.nombre='';
                this.descripcion='';
                this.codigoProductoServicio = '';
                this.codigoActividadEconomica = '';
            },
            abrirModal(modelo, accion, data = []){
                console.log("REGISTRAR");
                switch(modelo){
                    case "categoria":
                    {
                        switch(accion){
                            case 'registrar':
                            {
                                this.modal = 1;
                                this.tituloModal = 'Registrar Categoría de la Bebida';
                                this.nombre= '';
                                this.descripcion = '';
                                this.codigoProductoServicio = '';
                                this.codigoActividadEconomica = '';
                                this.tipoAccion = 1;
                                break;
                            }
                            case 'actualizar':
                            {
                                //console.log(data);
                                this.modal=1;
                                this.tituloModal='Actualizar categoría de la Bebida';
                                this.tipoAccion=2;
                                this.categoria_id=data['id'];
                                this.nombre = data['nombre'];
                                this.descripcion= data['descripcion'];
                                this.codigoProductoServicio = data['codigo'];
                                this.codigoActividadEconomica = data['actividadEconomica'];
                                break;
                            }
                        }
                    }
                }
            }
        },
        mounted() {
            this.listarCategoria(1,this.buscar,this.criterio);
            this.consultaProductosServicios();
            this.consultaActividadEconomica();
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
