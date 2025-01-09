<template>
    <main class="main">

    <!--<Toolbar class="mb-4">
        <template #start>
            <Button label="Nuevo" icon="pi pi-plus" class="p-button-sm p-button-success mr-2" @click="abrirModal('persona', 'registrar')"/>
            <Button label="Eliminar" icon="pi pi-trash" class="p-button-sm p-button-danger" @click="openDialog()"/>
        </template>

        <template #end>
            <Button :label="exportar_label" icon="pi pi-upload" class="p-button-sm p-button-help" />
        </template>
    </Toolbar>

    <DataTable
        :value="arrayPersona"
        :paginator="false"
        :rows="10"
        dataKey="id"
        :rowHover="true"
        responsiveLayout="scroll"
        :filters="filters"
        :selection.sync="usuariosSeleccionados"
        :loading="loading"
    >

        <template #header>
            <div class="table-header">
                <h4>Lista usuarios</h4>
                <span class="p-input-icon-left">
                    <i class="pi pi-search"/>
                    <InputText style="width: 13rem;" placeholder="Buscar ..." class="p-inputtext-sm" v-model="filters['global'].value"></InputText>
                </span>
            </div>
        </template>

        <template #empty>
            Sin usuarios encontrados ...
        </template>

        <template #loading>
            Cargando datos de los usuarios. Por fabor espere ...
        </template>

        <Column selectionMode="multiple" :styles="{'min-width': '3rem'}"></Column>
        <Column header="Foto">
            <template #body="slotProps">
                <div class="imagen-lista">
                    <ImagePreview v-if="slotProps.data.fotografia" :src="'img/usuarios/' + slotProps.data.fotografia" alt="Usuario" preview />
                    <ImagePreview v-else src="img/usuarios/defecto.jpg" alt="Usuario default" preview />
                </div>
            </template>
        </Column>
        <Column field="nombre" header="Nombre" sortable :styles="{'min-width': '7rem'}"></Column>
        <Column field="num_documento" header="Documento" sortable :styles="{'min-width': '5rem'}"></Column>
        <Column field="direccion" header="Dirección" sortable :styles="{'min-width': '7rem'}"></Column>
        <Column field="telefono" header="Teléfono" sortable :styles="{'min-width': '5rem'}"></Column>
        <Column field="email" header="Email" sortable :styles="{'min-width': '7rem'}"></Column>
        <Column field="usuario" header="Usuario" sortable :styles="{'min-width': '5rem'}"></Column>
        <Column field="rol" header="Rol" sortable :style="{'min-width': '5rem'}"></Column>
        <Column field="sucursal" header="Sucursal" sortable :styles="{'min-width': '5rem'}"></Column>
        <Column field="condicion" header="Estado" sortable :styles="{'min-width': '4rem'}">
            <template #body="slotProps">
                <Tag v-if="slotProps.data.condicion == 1" class="mr-2" icon="pi pi-check" severity="success" value="Activo"></Tag>
                <Tag v-else-if="slotProps.data.condicion == 0" class="mr-2" icon="pi pi-times" severity="danger" value="Desactivado"></Tag>
            </template>
        </Column>
        <Column header="Opciones" :styles="{'min-width':'9rem', 'max-width':'10rem'}">
            <template #body>
                <div class="botones_espacio">
                    <Button class="p-button-sm p-button-warning" type="button" icon="pi pi-pencil" @click="actualizarUsuario" />
                    <Button class="p-button-sm p-button-danger" type="button" icon="pi pi-ban" @click="actualizarUsuario" />
                </div>
            </template>
        </Column>

    </DataTable>

    <Dialog
        :visible.sync="nuevoUsuarioDialog"
        :modal="true"
        :position="posicionDialog"
    >

        <template #header>
            <div class="titulo-modal">
                <i class="pi pi-users sidebar-icon"></i>
                <h4 class="sidebar-title">Nuevo Usuario</h4>
            </div>
        </template>

        <div class="p-fluid p-formgrid p-grid">

            <div class="p-field p-col-12 p-md-6">
                <div class="selector-imagen">
                    <div style="display: flex; justify-content: center;">
                        <img v-if="imagen == ''" src="img/usuarios/defecto.jpg" alt="Usuario default" class="product-image"  />
                        <img v-else :src="imagen" alt="Foto Usuario" class="product-image"  />
                    </div>
                    <FileUpload mode="basic" name="demo[]" accept="image/*" :maxFileSize="5000000" :customUpload="true" @select="onSelect" ref="fileUpload" chooseLabel="Fotografia"/>
                </div>
            </div>

            <div class="p-col-12 p-md-6">
                <div class="p-grid" style="margin-top: 0rem;">
                    <div class="p-field p-col-12">
                        <div class="p-inputgroup">
                            <span class="p-inputgroup-addon">
                                <i class="pi pi-user"></i>
                            </span>
                            <InputText placeholder="Nombre Completo" class="p-inputtext-sm" v-model="form.nombreCompleto" :class="{'p-invalid': submitted && v$.form.nombreCompleto.$invalid}" />
                        </div>
                        <small class="p-error" v-if="(submitted && v$.form.nombreCompleto.required.$invalid)"><strong>Nombre Completo es obligatorio.</strong></small>
                    </div>

                    <div class="p-field p-col-12">
                        <div class="p-inputgroup">
                            <span class="p-inputgroup-addon">
                                <i class="pi pi-google"></i>
                            </span>
                            <InputText placeholder="Correo electrónico" class="p-inputtext-sm" v-model="form.email" :class="{'p-invalid': submitted && v$.form.email.$invalid}" />
                        </div>
                        <small class="p-error" v-if="(submitted && v$.form.email.required.$invalid)"><strong>Correo Electrónico es obligatorio.</strong></small>
                        <small class="p-error" v-if="(submitted && v$.form.email.email.$invalid)"><strong>Correo Electrónico inválido.</strong></small>
                    </div>

                    <div class="p-field p-col-12">
                        <div class="p-inputgroup">
                            <span class="p-inputgroup-addon">
                                <i class="pi pi-user"></i>
                            </span>
                            <InputText placeholder="Teléfono" class="p-inputtext-sm" v-model="form.telefono" :class="{'p-invalid': submitted && v$.form.telefono.$invalid}" />
                        </div>
                        <small class="p-error" v-if="(submitted && v$.form.telefono.required.$invalid)"><strong>Teléfono es obligatorio.</strong></small>
                        <small class="p-error" v-if="(submitted && v$.form.telefono.numeric.$invalid)"><strong>Solo se aceptan números.</strong></small>
                        <small class="p-error" v-if="(submitted && v$.form.telefono.minLength.$invalid)"><strong>Minimo 7 dígitos.</strong></small>
                        <small class="p-error" v-if="(submitted && v$.form.telefono.maxLength.$invalid)"><strong>Maximo 8 dígitos.</strong></small>
                    </div>        
                </div>
            </div>

            <div class="p-field p-col-12 p-md-6">
                <Dropdown class="p-inputtext-sm" v-model="form.tipo_documento" :options="arrayDocumentos" optionLabel="nombre" placeholder="Tipos de documento" :class="{'p-invalid': submitted && v$.form.tipo_documento.$invalid}"/>
                <small class="p-error" v-if="(submitted && v$.form.tipo_documento.required.$invalid)"><strong>Tipo de Documento es obligatorio.</strong></small>
            </div>
            <div class="p-field p-col-12 p-md-6">
                <div class="p-inputgroup">
                    <span class="p-inputgroup-addon">
                        <i class="pi pi-folder-open"></i>
                    </span>
                    <InputText placeholder="Número Documento" class="p-inputtext-sm" v-model="form.num_documento" :class="{'p-invalid': submitted && v$.form.num_documento.$invalid}"/>
                </div>
                <small class="p-error" v-if="(submitted && v$.form.num_documento.required.$invalid)"><strong>Número de Documento es obligatorio.</strong></small>
            </div>

            
            <div class="p-field p-col-12 p-md-6">
                <Dropdown class="p-inputtext-sm" v-model="form.sucursal" :options="arraySucursal" optionLabel="nombre" placeholder="Sucursal asignada" :class="{'p-invalid': submitted && v$.form.sucursal.$invalid}"/>
                <small class="p-error" v-if="(submitted && v$.form.sucursal.required.$invalid)"><strong>Sucursal es obligatorio.</strong></small>
            </div>
            <div class="p-field p-col-12 p-md-6">
                <Dropdown class="p-inputtext-sm" v-model.trim="form.rol" :options="arrayRol" optionLabel="nombre" placeholder="Roles" :class="{'p-invalid': submitted && v$.form.rol.$invalid}"/>
                <small class="p-error" v-if="(submitted && v$.form.rol.required.$invalid)"><strong>Rol de Usuario es obligatorio.</strong></small>
            </div>
            
            <div class="p-field p-col-12 p-md-6">
                <div class="p-inputgroup">
                    <span class="p-inputgroup-addon">
                        <i class="pi pi-user"></i>
                    </span>
                    <InputText placeholder="Nombre Usuario" class="p-inputtext-sm" v-model="form.nombreUsuario" :class="{'p-invalid': submitted && v$.form.nombreUsuario.$invalid}" />
                </div>
                <small class="p-error" v-if="(submitted && v$.form.nombreUsuario.required.$invalid)"><strong>Usuario es obligatorio.</strong></small>
            </div>
            <div class="p-field p-col-12 p-md-6">
                <div class="p-inputgroup">
                    <span class="p-inputgroup-addon">
                        <i class="pi pi-shield"></i>
                    </span>
                    <Password placeholder="Contraseña" class="p-inputtext-sm" v-model="form.password" toggleMask :class="{'p-invalid': submitted && v$.form.password.$invalid}"></Password>
                </div>
                <small class="p-error" v-if="(submitted && v$.form.password.required.$invalid)"><strong>Contraseña es obligatorio.</strong></small>
            </div>

            <div class="p-field p-col-12">
                <Textarea
                    placeholder="Direccion del domicilio ..."
                    class="p-inputtext-sm"
                    v-model="form.direccion"
                    rows="1"
                    cols="20"
                    :autoResize="true"
                    :class="{'p-invalid': submitted && v$.form.direccion.$invalid}"
                />
                <small class="p-error" v-if="(submitted && v$.form.direccion.required.$invalid)"><strong>Dirección es obligatorio.</strong></small>
            </div>

        </div>

        <template #footer >
            <div class="contenedor-footer">
                <div class="contenedor-button-footer">
                    <Button label="Cerrar" icon="pi pi-times" class="p-button-sm p-button-raised p-button-danger" @click="hideDialog"/>
                </div>
                <div class="contenedor-button-footer">
                    <Button label="Guardar" icon="pi pi-check" class="p-button-sm p-button-raised p-button-success" @click="registrarUsuario"/>
                </div>
            </div>
        </template>
    </Dialog>-->



            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Usuarios
                    <button type="button" @click="abrirModal('persona', 'registrar')" class="btn btn-secondary">
                        <i class="icon-plus"></i>&nbsp;Nuevo
                    </button>
                    <button type="button" @click="cargarReporteUsuariosExcel()" class="btn btn-info">
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
                                    <option value="email">Email</option>
                                    <option value="telefono">Teléfono</option>
                                    <option value="nombre">Sucursal</option>
                                </select>
                                <input type="text" v-model="buscar" @keyup="listarPersona(1, buscar, criterio)"
                                    class="form-control" placeholder="Texto a buscar">
                                <!--button-- type="submit" @click="listarPersona(1,buscar,criterio)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</!--button-->
                            </div>
                        </div>
                    </div>
                <div class="table-responsive">

                    <table class="table table-bordered table-striped table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>Foto</th>
                                <th>Nombre</th>
                                <th>Tipo Documento</th>
                                <th>Número</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th>Sucursal</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="persona in arrayPersona" :key="persona.id">
                                <td class="text-center">
                                    <img :src="'img/usuarios/' + persona.fotografia + '?t=' + new Date().getTime()" width="50" height="50"
                                        v-if="persona.fotografia" ref="imagen">
                                    <!--img :src="'img/usuarios/' + persona.fotografia" width="50" height="50"
                                        v-if="persona.fotografia" ref="imagen"-->
                                    <img :src="'img/usuarios/' + 'defecto.jpg'" width="50" height="50" v-else ref="imagen">
                                </td>
                                <td v-text="persona.nombre"></td>
                                <td v-text="getTipoDocumentoText(persona.tipo_documento)"></td>
                                <td v-text="persona.num_documento"></td>
                                <td v-text="persona.telefono"></td>
                                <td v-text="persona.email"></td>
                                <td v-text="persona.usuario"></td>
                                <td v-text="persona.rol"></td>
                                <td v-text="persona.sucursal"></td>
                                <td>
                                    <button type="button" @click="abrirModal('persona', 'actualizar', persona)"
                                        class="btn btn-warning btn-sm">
                                        <i class="icon-pencil"></i>
                                    </button> &nbsp;
                                    <template v-if="persona.condicion">
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
                            </tr>
                        </tbody>
                    </table>
                    </div>
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="pagination.current_page > 1">
                                <a class="page-link" href="#"
                                    @click.prevent="cambiarPagina(pagination.current_page - 1, buscar, criterio)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page"
                                :class="[page == isActived ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(page, buscar, criterio)"
                                    v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#"
                                    @click.prevent="cambiarPagina(pagination.current_page + 1, buscar, criterio)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <!-- Fin ejemplo de tabla Listado -->
        <!--Inicio del modal agregar/actualizar-->
        <div class="modal fade" tabindex="-1" :class="{ 'mostrar': modal }" role="dialog" aria-labelledby="myModalLabel"
     style="display: none;" aria-hidden="true">
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
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Columna izquierda -->
                            <div class="form-group">
                                <label class="form-control-label" for="text-input"><strong>Nombre(*)</strong></label>
                                <input type="text" v-model="nombre" class="form-control" placeholder="Nombre de la persona">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="text-input"><strong>Tipo documento</strong></label>
                                <select v-model="tipo_documento" class="form-control">
                                            <option value="" disabled>Selecciona una tipo de documento</option>
                                            <option value="1">CI - CEDULA DE IDENTIDAD</option>
                                            <option value="2">CEX - CEDULA DE IDENTIDAD DE EXTRANJERO</option>
                                            <option value="5">NIT - NÚMERO DE IDENTIFICACIÓN TRIBUTARIA</option>
                                            <option value="3">PAS - PASAPORTE</option>
                                            <option value="4">OD - OTRO DOCUMENTO DE IDENTIDAD</option>   
                                        </select> 
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="email-input"><strong>Teléfono</strong></label>
                                <input type="email" v-model="telefono" class="form-control" placeholder="Teléfono">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="email-input"><strong>Rol</strong></label>
                                <select v-model="idrol" class="form-control">
                                    <option value="0" disabled>Seleccione</option>
                                    <option v-for="role in arrayRol" :key="role.id" :value="role.id"
                                        v-text="role.nombre"></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="email-input"><strong>Usuario</strong></label>
                                <input type="text" v-model="usuario" class="form-control" placeholder="Nombre del usuario">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="email-input"><strong>Fotografía</strong></label>
                                <div class="row">
                                    <div class="col-md-8">
                                        <input type="file" @change="obtenerFotografia" class="form-control" placeholder="fotografia usuario" ref="fotografiaInput">
                                    </div>
                                    <div class="col-md-4">
                                        <figure>
                                            <img :src="imagen" width="50" height="50" alt="Foto usuario">
                                        </figure>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Columna derecha -->
                            <div class="form-group">
                                <label class="form-control-label" for="email-input"><strong>Dirección</strong></label>
                                <input type="email" v-model="direccion" class="form-control" placeholder="Dirección">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="email-input"><strong>Número documento</strong></label>
                                <input type="email" v-model="num_documento" class="form-control" placeholder="Número de documento">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="email-input"><strong>Email</strong></label>
                                <input type="email" v-model="email" class="form-control" placeholder="Email">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="email-input"><strong>Sucursal</strong></label>
                                <select v-model="idsucursal" class="form-control">
                                    <option value="0" disabled>Seleccione</option>
                                    <option v-for="sucursal in arraySucursal" :key="sucursal.id" :value="sucursal.id"
                                        v-text="sucursal.nombre"></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label" for="email-input"><strong>Clave</strong></label>
                                <input type="password" v-model="password" class="form-control" placeholder="Clave del usuario">
                            </div>
                        </div>
                    </div>

                    
                    <div v-show="errorPersona" class="form-group div-error">
                        <div class="text-center text-error">
                            <div v-for="error in errorMostrarMsjPersona" :key="error" v-text="error"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                <button type="button" v-if="tipoAccion == 1" class="btn btn-primary" @click="registrarPersona()">Guardar</button>
                <button type="button" v-if="tipoAccion == 2" class="btn btn-primary" @click="actualizarPersona()">Actualizar</button>
            </div>
        </div>
    </div>
</div>

        <!--Fin del modal-->
    </main>
</template>

<script>
import useVuelidate from '@vuelidate/core';
import { email, maxLength, minLength, numeric, required } from '@vuelidate/validators';

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Toolbar from 'primevue/toolbar';
import ImagePreview from 'primevue/imagepreview';
import Dialog from 'primevue/dialog';
import Textarea from 'primevue/textarea';
import FileUpload from 'primevue/fileupload';
import Password from 'primevue/password';
import Dropdown from 'primevue/dropdown';
import {FilterMatchMode} from 'primevue/api';

export default {
    setup () { return { v$: useVuelidate() } },
    data() {
        return {
            // Vuelidate
            form: {
                nombreCompleto: '',
                email: '',
                telefono: '',
                tipo_documento: '',
                num_documento: '',
                sucursal: '',
                rol: '',
                nombreUsuario: '',
                password: '',
                direccion: ''
            },
            /* PrimeVue variable */
            exportar_label: 'Exportar',
            filters: {},
            usuariosSeleccionados: null,
            nuevoUsuarioDialog: false,
            submitted: false,
            arrayDocumentos: [
                {nombre: 'CI - CEDULA DE IDENTIDAD', id: '1'},
                {nombre: 'CEX - CEDULA DE IDENTIDAD EX...', id: '2'},
                {nombre: 'PAS - PASAPORTE', id: '3'},
                {nombre: 'OD - OTRO DOCUMENTO', id: '4'},
                {nombre: 'NIT - NÚMERO DE IDENTIFICACI...', id: '5'},
            ],
            posicionDialog: 'center',
            loading: false,
            
            // ----------------------------------

            persona_id: 0,
            nombre: '',
            tipo_documento: '',
            num_documento: '',
            direccion: '',
            telefono: '',
            email: '',
            usuario: '',
            password: '',
            fotografia: '',
            fotoMuestra: '',
            idrol: '',
            idsucursal: '',
            idpuntoventa: '',
            arrayPersona: [],
            arrayRol: [],
            arraySucursal: [],
            arrayPuntoVenta: [],
            modal: 0,
            tituloModal: '',
            tipoAccion: 0,
            errorPersona: 0,
            errorMostrarMsjPersona: [],
            pagination: {
                'total': 0,
                'current_page': 0,
                'per_page': 0,
                'last_page': 0,
                'from': 0,
                'to': 0,
            },
            offset: 3,
            criterio: 'nombre',
            buscar: ''
        }
    },

    validations() {
        return {
            form: {
                nombreCompleto: {
                    required,
                },
                email: {
                    email,
                    required
                },
                telefono: { 
                    required, 
                    numeric,
                    minLength: minLength(7),
                    maxLength:maxLength(8)
                },
                tipo_documento: {
                    required
                },
                num_documento: {
                    required
                },
                sucursal: {
                    required
                },
                rol: {
                    required
                },
                nombreUsuario: {
                    required
                },
                password: {
                    required
                },
                direccion: {
                    required
                }
            }
        }
    },

    components: {
        DataTable,
        Column,
        InputText,
        Button,
        Tag,
        Toolbar,
        ImagePreview,
        Dialog,
        Textarea,
        FileUpload,
        Password,
        Dropdown
    },

    computed: {
        isMobile() {
            return window.innerWidth <= 576;
        },

        isTablet() {
            return window.innerWidth <= 1280;
        },

        isMonitor() {
            return window.innerWidth >= 1367;
        },

        isActived: function () {
            return this.pagination.current_page;
        },
        //Calcula los elementos de la paginación
        pagesNumber: function () {
            if (!this.pagination.to) {
                return [];
            }

            var from = this.pagination.current_page - this.offset;
            if (from < 1) {
                from = 1;
            }

            var to = from + (this.offset * 2);
            if (to >= this.pagination.last_page) {
                to = this.pagination.last_page;
            }

            var pagesArray = [];
            while (from <= to) {
                pagesArray.push(from);
                from++;
            }
            return pagesArray;

        },
        imagen() {
            return this.fotoMuestra;
        },
        filteredPuntosVenta(){
            if (this.idsucursal === 0) {
                return [];
            } else {
                return this.arrayPuntoVenta.filter(puntoventa => puntoventa.idsucursal === this.idsucursal);
            }
        }
    },
    methods: {

        actualizarUsuario() {
            console.log('Abriendo desplegable')
        },

        async registrarUsuario () {
            this.submitted = true;
            const result = await this.v$.$validate()

            if (!result) {
                console.log('Formulario incompleto:');
                console.log(this.form);
                return
            }

            let me = this;
            let formData = new FormData();

            formData.append('nombre', me.form.nombreCompleto);
            formData.append('email', me.form.email);
            formData.append('telefono', me.form.telefono);
            formData.append('tipo_documento', me.form.tipo_documento.id);
            formData.append('num_documento', me.form.num_documento);
            formData.append('idsucursal', me.form.sucursal.id);
            formData.append('idrol', me.form.rol.id);
            formData.append('usuario', me.form.nombreUsuario);
            formData.append('password', me.form.password);
            formData.append('direccion', me.form.direccion);
            formData.append('fotografia', me.fotografia);

            axios.post('/user/registrar', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(function (response) {
                swal(
                    'REGISTRO EXITOSO',
                    'Usuario Agregado',
                    'success'
                );
                me.hideDialog();
                me.listarPersona(1, '', 'nombre');
            }).catch(function (error) {
                swal(
                    'REGISTRO EXITOSO',
                    'Usuario Agregado',
                    'success'
                );
                console.log(error);
            });
        },

        checkScreenSize() {
            if (window.innerWidth <= 1367) {
                this.posicionDialog = 'right';
            } else {
                this.posicionDialog = 'center';
            }
        },

        actualizarLabelExportar() {
            console.log('CAMBIANDO A CELULAR')
            if (this.isMobile) {
                this.exportar_label = '';
            }
        },

        initFilters() {
            this.filters = {
                'global': {value: null, matchMode: FilterMatchMode.CONTAINS},
            }
        },

        openDialog() {
            this.submitted = false;
            this.nuevoUsuarioDialog = true;
            console.log(this.form);
        },

        hideDialog() {
            this.submitted = false;
            this.nuevoUsuarioDialog = false;

            this.$refs.fileUpload.clear();
            this.form = {
                nombreCompleto: '',
                email: '',
                telefono: '',
                tipo_documento: '',
                num_documento: '',
                sucursal: '',
                rol: '',
                nombreUsuario: '',
                password: '',
                direccion: ''
            };
            this.fotoMuestra = 'img/usuarios/defecto.jpg';
        },

        onSelect(event) {
            let file = event.files[0];

            let fileType = file.type;
            if (fileType !== 'image/png' && fileType !== 'image/jpeg') {
                alert('Por favor, seleccione una imagen en formato PNG o JPG.');
                return;
            }

            this.fotografia = file;
            this.mostrarFoto(file);
            
            this.$refs.fileUpload.clear();
        },

        listarPersona(page, buscar, criterio) {
            let me = this;
            var url = '/user?page=' + page + '&buscar=' + buscar + '&criterio=' + criterio;
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                me.arrayPersona = respuesta.personas;
                //me.pagination = respuesta.pagination;
                me.loading = false;
                console.log('LOADING: ', me.loading);
            })
                .catch(function (error) {
                    console.log(error);
                });
        },
        selectRol() {
            let me = this;
            var url = '/rol/selectRol';
            axios.get(url).then(function (response) {
                //console.log(response);
                var respuesta = response.data;
                me.arrayRol = respuesta.roles;
            })
                .catch(function (error) {
                    console.log(error);
                });
        },
        
        selectSucursal() {
            let me = this;
            var url = '/sucursal/selectSucursal';
            axios.get(url).then(function (response) {
                //console.log(response);
                var respuesta = response.data;
                me.arraySucursal = respuesta.sucursales;
            })
                .catch(function (error) {
                    console.log(error);
                });
        },

        obtenerPuntosDeVenta(){
            if (this.idsucursal === '0'){
                return;
            }

            axios.get(`/api/puntosDeVenta/${this.idsucursal}`)
            .then(response => {
                this.arrayPuntoVenta = response.data;
            })
            .catch(error => {
                console.log('Error al obtener los puntos de venta: ', error);
            })
        },

        cambiarPagina(page, buscar, criterio) {
            let me = this;
            //Actualiza la página actual
            me.pagination.current_page = page;
            //Envia la petición para visualizar la data de esa página
            me.listarPersona(page, buscar, criterio);
        },
        obtenerFotografia(event) {

            let file = event.target.files[0];
            console.log('Obteniendo fotografia: ', file);

            let fileType = file.type;
                // Validar si el archivo es una imagen en formato PNG o JPG
                if (fileType !== 'image/png' && fileType !== 'image/jpeg') {
                    alert('Por favor, seleccione una imagen en formato PNG o JPG.');
                    return;
                }
            this.fotografia = file;
            this.mostrarFoto(file);

        },
        mostrarFoto(file) {

            let reader = new FileReader();

            reader.onload = (file) => {
                this.fotoMuestra = file.target.result;
            }
            reader.readAsDataURL(file);
        },

        /*registrarPersona() {
            if (this.validarPersona()) {
                return;
            }

            let me = this;
            let formData = new FormData();

            formData.append('nombre', this.nombre);
            formData.append('tipo_documento', this.tipo_documento);
            formData.append('num_documento', this.num_documento);
            formData.append('direccion', this.direccion);
            formData.append('telefono', this.telefono);
            formData.append('email', this.email);
            formData.append('idrol', this.idrol);
            formData.append('idsucursal', this.idsucursal);
            formData.append('idpuntoventa', this.idpuntoventa);
            formData.append('usuario', this.usuario);
            formData.append('password', this.password);
            formData.append('fotografia', this.fotografia);

            //for (let [key, value] of formData.entries()) 
            //{
            //    console.log(key, value);
            //}

            axios.post('/user/registrar', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }

            }).then(function (response) {
                swal(
                    'REGISTRO ÉXITOSO',
                    'Usuario Añadido',
                    'success'
                    );
                me.cerrarModal();
                me.listarPersona(1, '', 'nombre');
            }).catch(function (error) {
                swal(
                    'REGISTRO FALLIDO',
                    'Intente de Nuevo',
                    'warning'
                );
                console.log(error);
            });
        },*/

        actualizarPersona() {
            if (this.validarPersona()) {
                return;
            }

            console.log(this.fotografia);
            let me = this;
            let formData = new FormData();
            formData.append('nombre', this.nombre);
            formData.append('tipo_documento', this.tipo_documento);
            formData.append('num_documento', this.num_documento);
            formData.append('direccion', this.direccion);
            formData.append('telefono', this.telefono);
            formData.append('email', this.email);
            formData.append('idrol', this.idrol);
            formData.append('idsucursal', this.idsucursal);
            formData.append('idpuntoventa', this.idpuntoventa);
            formData.append('usuario', this.usuario);
            formData.append('password', this.password);
            formData.append('fotografia', this.fotografia);
            formData.append('id', this.persona_id);

            axios.post('/user/actualizar', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(function (response) {
                swal(
                    'ACTUALIZACIÓN ÉXITOSA',
                    'Usuario Actualizado',
                    'success'
                    );
                me.cerrarModal();
                me.listarPersona(1, '', 'nombre');
            }).catch(function (error) {
                swal(
                    'ACTUALIZACIÓN FALLIDA',
                    'Intente de Nuevo',
                    'warning'
                );
                console.log(error);
            });
        },
        validarPersona() {
            this.errorPersona = 0;
            this.errorMostrarMsjPersona = [];

            if (!this.nombre) this.errorMostrarMsjPersona.push("El nombre de la pesona no puede estar vacío.");
            if (!this.usuario) this.errorMostrarMsjPersona.push("El nombre de usuario no puede estar vacío.");
            if (!this.password) this.errorMostrarMsjPersona.push("La password del usuario no puede estar vacía.");
            if (this.idrol == 0) this.errorMostrarMsjPersona.push("Seleccione una Role.");
            if (this.errorMostrarMsjPersona.length) this.errorPersona = 1;

            return this.errorPersona;
        },
        cerrarModal() {
            //Usando referencia en el file para limpiarlo al cerrar el modal
            let fileInput = this.$refs.fotografiaInput;
            fileInput.value = '';

            this.modal = 0;
            this.tituloModal = '';
            this.nombre = '';
            this.tipo_documento = 'DNI';
            this.num_documento = '';
            this.direccion = '';
            this.telefono = '';
            this.email = '';
            this.usuario = '';
            this.password = '';
            this.fotografia = fileInput; //Pasando el valor limpio de la referencia
            this.fotoMuestra = 'img/usuarios/defecto.jpg';
            this.idrol = 0;
            this.idsucursal = 0;
            this.idpuntoventa = '';
            this.errorPersona = 0;
        },
        abrirModal(modelo, accion, data = []) {
            this.selectRol();
            this.selectSucursal();
            switch (modelo) {
                case "persona":
                    {
                        switch (accion) {
                            case 'registrar':
                                {
                                    this.modal = 1;
                                    this.tituloModal = 'Registrar Usuario';
                                    this.nombre = '';
                                    this.tipo_documento = 'DNI';
                                    this.num_documento = '';
                                    this.direccion = '';
                                    this.telefono = '';
                                    this.email = '';
                                    this.usuario = '';
                                    this.password = '';
                                    this.fotografia = '';
                                    this.idrol = 0;
                                    this.idsucursal = 0;
                                    this.idpuntoventa = '';
                                    this.tipoAccion = 1;
                                    break;
                                }
                            case 'actualizar':
                                {
                                    //console.log(data['fotografia']);
                                    this.modal = 1;
                                    this.tituloModal = 'Actualizar Usuario';
                                    this.tipoAccion = 2;
                                    this.persona_id = data['id'];
                                    this.nombre = data['nombre'];
                                    this.tipo_documento = data['tipo_documento'];
                                    this.num_documento = data['num_documento'];
                                    this.direccion = data['direccion'];
                                    this.telefono = data['telefono'];
                                    this.email = data['email'];
                                    this.usuario = data['usuario'];
                                    this.password = data['password'];
                                    this.fotografia = data['fotografia'];
                                    this.fotoMuestra = data['fotografia'] ? 'img/usuarios/' + data['fotografia'] : 'img/usuarios/defecto.jpg';
                                    this.idrol = data['idrol'];
                                    this.idsucursal = data['idsucursal'];
                                    this.idpuntoventa = data['idpuntoventa'];
                                    break;
                                }
                        }
                    }
            }
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

                    axios.put('/user/desactivar', {
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

                    axios.put('/user/activar', {
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
        cargarReporteUsuariosExcel()
        {
            window.open('/user/listarReporteUsuariosExcel', '_blank');
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
        }
    },
    watch: {
        idsucursal(){
            this.obtenerPuntosDeVenta();
        }
    },

    created() {
        //this.$v = useVuelidate();
        this.initFilters();
    },

    mounted() {
        this.loading = true;
        window.addEventListener('resize', this.actualizarLabelExportar);
        this.checkScreenSize();
        window.addEventListener('resize', this.checkScreenSize);

        this.listarPersona(1, this.buscar, this.criterio);
        this.selectRol();
        this.selectSucursal();
    },

    beforeDestroy() {
        window.removeEventListener('resize', this.actualizarLabelExportar);
        window.removeEventListener('resize', this.checkScreenSize);
    }
}
</script>

<style >
.p-image-preview {
    max-width: 55vw !important;
    max-height: 55vh !important;
}

.p-image-toolbar {
    padding-top: 65px;
}
</style>

<style scoped>

/* Header Toolbar */
>>> .p-toolbar {
    padding: 0.5rem;
    margin: 0.5rem 0.2rem;
}

>>> .mb-4 {
    margin-bottom: 0.5rem !important;
}

/* Header Table */
.table-header {
    display: flex;
    justify-content: space-between;
    width: 100%;
    align-items: center;
}

>>> .p-datatable .p-datatable-header {
    padding: 0.5rem 1rem;
}

/* Preview Image */
>>> .p-image-preview-indicator {
    width: 75px;
    height: 75px;
    border-radius: 10px;
}

.imagen-lista >>> img {
    width: 75px;
    height: 75px;
    border-radius: 10px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
}

/* Opciones Table */
.botones_espacio {
    display: flex;
    justify-content: space-between;
    width: 100%;
    align-items: center;
}

.botones_espacio >>> .p-button-sm {
    padding: 0.6rem 0.8rem;
}

/* Dailog */
>>> .p-dialog-header {
    padding: 1rem 1rem 0.5rem 1rem;
}

@media (min-width: 1024px) {
    >>> .p-dialog {
        max-width: 55%;
        max-height: 85%;
    }
}

.p-dialog-right >>> .p-dialog {
    margin: 4.2rem 0.75rem 0.75rem 0.75rem;
}

.selector-imagen {
    display: grid;
    justify-content: center;
}

.selector-imagen >>> .p-button {
    width: 135px !important;
    padding: 0.5rem 1rem;
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
}

.product-image {
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    width: 135px;
    /*height: 115px;*/
}

.titulo-modal {
    display: flex;
    align-items: center;
}

.sidebar-icon {
    font-size: 1.2rem;
    margin-right: 10px;
}

.sidebar-title {
    font-size: 1.2rem;
    margin: 0;
}

>>> .p-dialog-content {
    padding: 0 1rem 0 1rem;
}

>>> .p-dialog-footer {
    padding: 0.5rem 1rem 1rem 1rem;
}

.contenedor-footer {
    display: flex;
    justify-content: space-between;
    padding: 0 ;
}

.contenedor-button-footer button {
    margin: 0 0 0 0;
}

/* ---------------- Antiguos estilos -------------- */
    .modal-content {
        width: 100% !important;
        position: absolute !important;
    }

    .mostrar {
        display: list-item !important;
        opacity: 1 !important;
        position: absolute !important;
        background-color: #3c29297a !important;
    }

    .div-error {
        display: flex;
        justify-content: center;
    }

    .text-error {
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
</style>
