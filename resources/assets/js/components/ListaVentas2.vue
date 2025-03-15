<template>
    <main class="main">
        <div class="p-p-4 p-mx-auto" style="max-width: 100%;"> 

        
        <Panel header="Reporte Ventas" :toggleable="false">
            <span class="badge bg-secondary" id="comunicacionSiat" style="color: white;">Desconectado</span>
            <span class="badge bg-secondary" id="cuis">CUIS: Inexistente</span>
            <span class="badge bg-secondary" id="cufd">No existe cufd vigente</span>
            <span class="badge bg-secondary" id="direccion" v-show="mostrarDireccion">No hay dirección registrada</span>
            <span class="badge bg-primary" id="cufdValor" v-show="mostrarCUFD">No hay CUFD</span>

            <template v-if="listado == 1">
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <select class="form-control col-md-3" v-model="criterio">
                                        <option value="tipo_comprobante">Tipo Comprobante</option>
                                        <option value="num_comprobante">Número Comprobante</option>
                                        <option value="fecha_hora">Fecha-Hora</option>
                                    </select>
                                    <input type="text" v-model="buscar" @keyup="listarVenta(1, buscar, criterio)"
                                        class="form-control" placeholder="Texto a buscar">
                                    <!--button type="submit" @click="listarVenta(1, buscar, criterio)" class="btn btn-primary"><i
                                            class="fa fa-search"></i> Buscar</button-->
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Opciones</th>
                                    <th>Vendedor</th>
                                    <th>Comprobante</th>
                                    <th>Cliente</th>
                                    <th>Documento</th>
                                    <th>Número Factura</th>
                                    <th>Fecha Hora</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="venta in arrayVenta" :key="venta.id_venta" 
                                    :class="{'table-success': venta.estado === 'Registrado', 
                                                'table-danger': venta.estado !== 'Registrado' && venta.usuario !== 'admin',
                                                'table-warning': venta.usuario === 'admin'}">
                                    <td>
                                        <button type="button" @click="verVenta(venta.id_venta)" class="btn btn-success btn-sm">
                                            <i class="icon-eye"></i>
                                        </button> &nbsp;

                                        <template v-if="venta.estado == 'Registrado'">
                                            <button type="button" class="btn btn-danger btn-sm" @click="desactivarVenta(venta.id)">
                                                <i class="icon-trash"></i>
                                            </button> &nbsp;
                                        </template>

                                        <button type="button" @click="imprimirTicket(venta.id_venta)" class="btn btn-info btn-sm">
                                            Imprimir Comanda
                                        </button>

                                        <template v-if="venta.estado == '2'">
                                            <button type="button" @click="abrirModalPago(venta.id_venta)" class="btn btn-warning btn-sm">
                                                Pagar
                                            </button>
                                        </template>
                                    </td>
                                    <td v-text="venta.usuario"></td>
                                    <td v-text="venta.tipo_comprobante"></td>
                                    <td v-text="venta.razonSocial"></td>
                                    <td v-text="venta.documentoid"></td>
                                    <td v-text="venta.num_comprobante"></td>
                                    <td v-text="venta.fecha_hora"></td>
                                    <td v-text="venta.total"></td>
                                    <td :class="getEstadoClass(venta.estado)" v-text="getEstadoText(venta.estado)"></td>
                                    <td>
                                        <template v-if="venta.tipo_comprobante === 'FACTURA'">
                                            <a @click="verificarFactura(venta.cuf, venta.numeroFactura)" target="_blank" class="btn btn-info">
                                                <i class="icon-note"></i>
                                            </a>
                                            <button class="btn btn-primary" type="button" @click="imprimirFactura(venta.factura_id, venta.correo)">
                                                <i class="icon-printer"></i>
                                            </button>
                                            <button class="btn btn-danger" type="button" @click="anularFactura(venta.factura_id, venta.cuf)">
                                                <i class="icon-close"></i>
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
                </template>


                <template v-else-if="listado == 2">
                    <div class="card-body">
                        <div class="form-group row border">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for=""><strong>Cliente</strong></label>
                                    <p v-text="cliente"></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><strong>Tipo Comprobante</strong></label>
                                    <p v-text="tipo_comprobante"></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><strong>Número Comprobante</strong></label>
                                    <p v-text="num_comprobante"></p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row border">
                            <div class="table-responsive col-md-12">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Artículo</th>
                                            <th>Precio</th>
                                            <th>Cantidad</th>
                                            <th>Descuento</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="arrayDetalle.length">
                                        <tr v-for="detalle in arrayDetalle" :key="detalle.id">
                                            <td v-text="detalle.nombre">
                                            </td>
                                            <td v-text="detalle.precio">
                                            </td>
                                            <td v-text="detalle.cantidad">
                                            </td>
                                            <td v-text="detalle.descuento">
                                            </td>
                                            <td>
                                                {{ detalle.precio * detalle.cantidad - detalle.descuento }}
                                            </td>
                                        </tr>
                                        <tr style="background-color: #CEECF5;">
                                            <td colspan="4" align="right"><strong>Total Neto:</strong></td>
                                            <td>Bs. {{ total }}</td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr>
                                            <td colspan="5">
                                                No hay articulos agregados
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <button type="button" @click="ocultarDetalle()" class="btn btn-secondary">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </template>
        </Panel>
        <!-- Modal de Pago -->
        <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentModalLabel">Opciones de Pago</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <form>
                                            <div class="form-group">
                                                <label for="documento"><i class="fa fa-money mr-2"></i> Documento:</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control" id="documento" v-model="documento" placeholder="Documento" @keyup.enter="fetchClienteData">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="cliente"><i class="fa fa-money mr-2"></i> Razón Social:</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control" id="cliente" v-model="cliente" placeholder="Razón Social">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <form>
                                            <div class="form-group">
                                                <label for="email"><i class="fa fa-money mr-2"></i> Correo Electrónico:</label>
                                                <div class="input-group mb-3">
                                                    <input type="email" class="form-control" id="email" v-model="email" placeholder="Email">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="numero-ticket"><i class="fa fa-money mr-2"></i> Numero Comanda:</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control" id="numero-ticket" v-model="num_comprob" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="numero-factura"><i class="fa fa-money mr-2"></i> Numero Factura:</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control" id="numero-factura" v-model="num_factura" readonly>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-body">
                        <template v-if="cambiar_pagina == 1">
                            <TabView>
                                <TabPanel header="Efectivo">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card shadow-sm">
                                                    <div class="card-body">
                                                        <form>
                                                            <div class="form-group">
                                                                <label for="montoEfectivo"><i class="fa fa-money mr-2"></i> Monto Recibido:</label>
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">{{ monedaVenta[1] }}</span>
                                                                    </div>
                                                                    <input type="number" class="form-control" id="montoEfectivo" v-model="recibido" placeholder="Ingrese el monto recibido">
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <label for="cambioRecibir"><i class="fa fa-exchange mr-2"></i> Entregar:</label>
                                                                <input type="text" class="form-control" id="cambioRecibir" placeholder="Se calculará automáticamente" :value="(recibido - totalReservaSeleccionada).toFixed(2)" readonly>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card shadow-sm">
                                                    <div class="card-body">
                                                        <div class="mb-3">
                                                            <h5 class="mb-0"> Detalle de Venta</h5>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span><i class="fa fa-dollar mr-2"></i> Monto:</span>
                                                            <span class="font-weight-bold">{{ Number(totalReservaSeleccionada).toFixed(2) }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span><i class="fa fa-tag mr-2 text-success"></i> Descuento:</span>
                                                            <span class="font-weight-bold text-success">0.00</span>
                                                        </div>
                                                        <hr>
                                                        <div class="d-flex justify-content-between">
                                                            <span><i class="fa fa-money mr-2"></i> Total:</span>
                                                            <span class="font-weight-bold h5">{{ Number(totalReservaSeleccionada).toFixed(2) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" @click="registrarVenta(1)" class="btn btn-success btn-block">
                                                    <i class="fa fa-check mr-2"></i> Registrar Pago
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </TabPanel>

                                <TabPanel header="QR">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div>
                                            <InputText v-model="alias" readonly style="display: none;" />
                                            <br>
                                            <label for="montoQR">Monto:</label>
                                            <span class="font-weight-bold">{{ Number(totalReservaSeleccionada).toFixed(2) }}</span>
                                            <br>
                                            <Button v-if="(idrol !== 1 || (idrol === 1  && tipo_entrega != 'Entregas'))" @click="generarQr" label="Generar QR" />
                                            
                                            <!-- Espacio para mostrar la imagen del código QR -->
                                            <div v-if="qrImage">
                                                <img :src="qrImage" alt="Código QR" />
                                            </div>

                                            <!-- Botón para verificar estado -->
                                            <Button @click="verificarEstado" v-if="qrImage" label="Verificar Estado de Pago" />

                                            <!-- Mostrar estado de transacción -->
                                            <div v-if="estadoTransaccion" class="p-card p-p-2">
                                                <div class="p-text-bold">Estado Actual:</div>
                                                <div>
                                                    <Badge :value="estadoTransaccion.objeto.estadoActual" :severity="badgeSeverity" />
                                                </div>
                                            </div>

                                            <!-- Botón para registrar la venta -->
                                            <button
                                                v-if="((idrol === 1 && tipo_entrega === 'Entregas')   || (idrol === 2 && estadoTransaccion && estadoTransaccion.objeto.estadoActual === 'PAGADO' ) || (idrol === 1 && tipo_entrega != 'Entregas' && estadoTransaccion && estadoTransaccion.objeto.estadoActual === 'PAGADO'  ))" 
                                                type="button" @click="registrarVenta(7)" class="btn btn-success btn-block">
                                                <i class="fa fa-check mr-2"></i> Confirmar Venta
                                            </button>
                                        </div>
                                    </div>
                                </TabPanel>

                                <TabPanel header="Tarjeta">
                                    <div>
                                        <div class="mt-4">
                                            <form>
                                                <div class="form-group">
                                                    <label for="numeroTarjeta"><i class="fa fa-credit-card mr-2"></i> Número de Tarjeta:</label>
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control" id="numeroTarjeta" v-model="numeroTarjeta" placeholder="Ingrese el número de tarjeta">
                                                    </div>
                                                </div>
                                                <button type="button" @click="registrarVenta(2)" class="btn btn-success btn-block"><i class="fa fa-check mr-2"></i> Confirmar</button>
                                            </form>
                                        </div>
                                    </div>
                                </TabPanel>
                            </TabView>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </main>
</template>

<script>

import InputSwitch from 'primevue/inputswitch';
import vSelect from 'vue-select';
import Button from 'primevue/button';
import Dropdown from 'primevue/dropdown';
import DataView from 'primevue/dataview';
import DataViewLayoutOptions from 'primevue/dataviewlayoutoptions';
import Badge from 'primevue/badge';
import Dialog from 'primevue/dialog';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import Panel from 'primevue/panel';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Sidebar from 'primevue/sidebar';
import Card from 'primevue/card';
import Menu from 'primevue/menu';

import { TileSpinner } from 'vue-spinners';

export default {
    data() {
        return {

            //qr
            alias: '',
            montoQR: 0,
            qrImage: '',
            aliasverificacion: '',
            estadoTransaccion: null,
            currency: 'BOB', // Define tu moneda

            // primeVue variables
            tipo_entrega: '',  // Inicializar como cadena vacía
            tipoEntregaOptions: ['Llevar', 'Aqui', 'Entregas'],

            paraLlevar: false,
            categoria_busqueda: '',
            arrayCategoriasMenu: [],
            arrayCategoriasProducto: [],
            arrayComidas: [],
            arrayBebidas: [],
            arrayMenu: [],
            layout: 'grid',
            activeIndex: 0,
            visibleFull: false,
            visiblePago: false,
            visibleRight: false,

            buttonStyle: {
                width: '200px',
            },

            ejemploCarrito: 9,

            items: [
                {
                    label: 'Categorias',
                    items: [{
                        label: 'Comidas',
                        icon: 'pi pi-refresh',
                        command: () => {
                            this.updateProducts('Comidas')
                            //this.$toast.add({severity:'success', summary:'Updated', detail:'Data Updated', life: 3000});
                        }
                    },
                    {
                        label: 'Bebidas',
                        icon: 'pi pi-times',
                        command: () => {
                            this.updateProducts('Bebidas')
                            //this.$toast.add({ severity: 'warn', summary: 'Delete', detail: 'Data Deleted', life: 3000});
                        }
                    }
                ]}
            ],

            

            // -----------------------

            venta_id: 0,
            idventaa: 0,
            idcliente: 0,
            usuarioAutenticado: null,
            puntoVentaAutenticado: null,
            cliente: '',
            email: '',
            mesa: 0,
            nombreCliente: '',
            documento: '',
            tipo_documento: '',
            complemento_id: '',
            descuentoAdicional: 0.00,
            descuentoGiftCard: '',
            tipo_comprobante: 'TICKET',
            observacion: '',
            serie_comprobante: '',
            last_comprobante: 0,
            num_comprob: "",
            num_factura: "",
            impuesto: 0.18,
            total: 0.0,
            totalImpuesto: 0.0,
            totalParcial: 0.0,
            arrayVenta: [],
            arrayCliente: [],
            arrayDetalle: [],
            arrayFactura: [],
            mostrarTipoComprobante: false,
            listado: 1,
            modal: 0,
            tituloModal: '',
            tipoAccion: 0,
            modal2: 0,
            tituloModal2: '',
            tipoAccion2: '',
            errorVenta: 0,
            errorMostrarMsjVenta: [],
            pagination: {
                'total': 0,
                'current_page': 0,
                'per_page': 0,
                'last_page': 0,
                'from': 0,
                'to': 0,
            },
            offset: 3,
            criterio: '',
            buscar: '',
            criterioA: '0',
            buscarA: '',
            arrayArticulo: [],
            codigoComida: 0,
            codigo: '',
            articulo: '',
            medida: '',
            codigoClasificador: '',
            codigoProductoSin: '',
            precio: 0,
            cantidad: 1,
            descuento: 0,
            descuentoProducto: 0,
            sTotal: 0,
            stock: 0,
            valorMaximoDescuento: '',
            mostrarDireccion: true,
            mostrarCUFD: true,
            mostrarEnviarPaquete: true,
            mostrarValidarPaquete: false,
            cafc: '',
            scodigomotivo: null,
            numeroTarjeta: null,
            casosEspeciales: false,
            mostrarCampoCorreo: false,
            leyendaAl: '',
            codigoExcepcion: 0,
            mostrarSpinner: false,
            metodoPago: '',
            monedaVenta: [],
            usuario_autenticado: '',
            //almacenes
            arrayAlmacenes: [],
            idAlmacen: 1,
            recibido: 0,
            efectivo: 0,
            cambiar_pagina: 1,
            idrol: 1,
            totalReservaSeleccionada: 0,
            mostrarSeleccionPersonal: false,
            arrayPersonales: [],
            idPersonalSeleccionado: null,
            totalComision: 0,
            comisionesPorPersonal: {},
            cambio: 0,
            faltante: 0,
            idtipo_pago: '',
            idtipo_venta: '1',
            tiempo_diaz: '',
            numero_cuotas: '',
            cuotas: [],//---para almacenar las fechas
            estadocrevent: 'activo',
            primera_cuota: '',
            habilitarPrimeraCuota: false,
            tipoPago: 'EFECTIVO',
            ventaSeleccionada : '',
            arrayProductos: [],
            arrayProductos: [],
            selectedProductType: 'menu',
            tiposPago: {
                        EFECTIVO: 1,
                        TARJETA: 2,
                        QR: 3
                        },
            // ------ DELIVERY
            telefono_delivery: '',
            direccion_delivery: '',
            pedido_delivery: '',
            listaSucursales: [],
            sucursalSeleccionada: 1,
            deliverySeleccionado: '', 
            idrol: '',
            idsucursalusuario: '',
            idsucursalventa: '',
            idusuario: '',
        }
    },
    components: {
        TileSpinner,
        vSelect,
        Button,
        Dropdown,
        DataView,
        DataViewLayoutOptions,
        Badge,
        Dialog,
        InputSwitch,
        TabView,
        TabPanel,
        Panel,
        InputText,
        InputNumber,
        Sidebar,
        Card,
        Menu,
    },

    watch: {
        'detalle.cantidad': function(newValue, oldValue) {
            // Aquí puedes agregar la lógica para actualizar la cantidad en arrayProductos
            // Puedes acceder al índice de arrayDetalle utilizando indexOf
            const index = this.arrayDetalle.indexOf(this.detalle);
            // Asegúrate de que el índice sea válido y luego actualiza la cantidad en arrayProductos
            if (index !== -1) {
            this.arrayFactura[index].cantidad = newValue;
            }
        },

        tipo_entrega(newVal) {
      console.log('Tipo de entrega seleccionado:', newVal);
    }
    },

    computed: {
        mostrarMesa() {
            return this.tipo_entrega === 'Aqui';
        },

        isActived: function () {
            return this.pagination.current_page;
        },

        anyProductSelected() {
            return this.arrayProductos.some(producto => producto.seleccionado);
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

        calcularTotal: function () {
            var resultado = 0.0;
            for (var i = 0; i < this.arrayDetalle.length; i++) {
                resultado += (this.arrayDetalle[i].precio * this.arrayDetalle[i].cantidad - this.arrayDetalle[i].descuento)
            }
            resultado -= this.descuentoAdicional;
            return resultado;
        },

        calcularSubTotal: function () {
            var resultado = 0.0;
            for (var i = 0; i < this.arrayDetalle.length; i++) {
                resultado = resultado + (this.arrayDetalle[i].precio * this.arrayDetalle[i].cantidad - this.arrayDetalle[i].descuento)
            }
            return resultado;
        },

        badgeSeverity() {
            if (this.estadoTransaccion && this.estadoTransaccion.objeto.estadoActual === 'PENDIENTE') {
                return 'danger'; // Rojo para estado PENDIENTE
            } else if (this.estadoTransaccion && this.estadoTransaccion.objeto.estadoActual === 'PAGADO') {
                return 'success'; // Verde para estado PAGADO
            } else {
                return 'info'; // Otros estados
            }
            }
    
    },
    methods: {

        toggle(event) {
            this.$refs.menu.toggle(event);
        },
        save() {
            this.$toast.add({severity: 'success', summary: 'Success', detail: 'Data Saved', life: 3000});
        },

        updateProducts(categoria) {
            if (categoria === 'Comidas') {
                //this.arrayMenu = obtenerProductos('Comidas');
                this.listarMenu(this.buscar, this.criterio);
            } else if (categoria === 'Bebidas') {
                //this.arrayMenu = obtenerProductos('Bebidas');
                this.listarProducto(1, this.buscar, this.criterio);
            }

        },

        verificarEstado() {
            axios.post('/qr/verificarestado', {
                alias: this.aliasverificacion,
            })
            .then(response => {
                this.estadoTransaccion = response.data;
            })
            .catch(error => {
                console.error(error);
            });
        },

        actualizarFechaHora() {
            const now = new Date();
            this.alias = now.toLocaleString();
        },

    generarQr() {
      this.aliasverificacion = this.alias;
      axios.post('/qr/generarqr', {
        alias: this.alias,
        monto: this.totalReservaSeleccionada
      })
      .then(response => {
        const imagenBase64 = response.data.objeto.imagenQr;
        this.qrImage = `data:image/png;base64,${imagenBase64}`;
      })
      .catch(error => {
        console.error(error);
      });

      this.alias = '';
      this.montoQR = 0;
    },

    verDetalle(producto) {
        console.log('PULSADO');
        console.log('Producto pulsado:', producto);
    },

    truncateAndCapitalize(text) {
        const maxLength = 14;
        text = text.length > maxLength ? text.substring(0, maxLength - 3) + '...' : text;
        return text.replace(/\b\w/g, (char) => char.toUpperCase());
    },

    editarMenu(event) {
        event.stopPropagation();
        console.log('EDITANDO MENU');
    },

        updateButtonStyle() {
            const windowWidth = window.innerWidth;
            //console.log(windowWidth);

            if (windowWidth <= 576) {
                this.buttonStyle.width = '145px';
            } else {
                this.buttonStyle.width = '200px';
            }
        },

        abrirCarrito() {
            console.log('abriendo carrito');
            this.abrirVentanaVenta();
            this.goToNextTab();
        },


        abrirVentanaVenta() {
            let me = this;

            me.listado = 3;
        },

        getCategoriasMenu() {
            let me = this;

            var url = '/categorias_menu/getAll';
            axios.get(url).then(function (response) {

                let respuesta = response.data;
                me.arrayCategoriasMenu = respuesta.categorias_menu;
                console.log('menu categorias:', me.arrayCategoriasMenu);
            })
            .catch(function (error) {
                console.log(error);
            });
        },

        getCategoriasProductos() {
            let me = this;
            var url = '/categoria/selectCategoria';
            axios.get(url).then(function (response) {

                let respuesta = response.data;
                me.arrayCategoriasProducto = respuesta.categorias;
                console.log('productos categorias:', me.arrayCategoriasProducto);
            })
            .catch(function (error) {
                console.log(error);
            });
        },

        scrollToSection() {
            $('html, body').animate({
                scrollTop: $('#seccionObjetivo').offset().top
            }, 50);
        },
        scrollToTop() {
            $('html, body').animate({
                scrollTop: 0
            }, 50);
        },

        atajoButton: function (event) {
            //console.log(event.keyCode);
            //console.log(event.ctrlKey);
            if (event.shiftKey && event.keyCode === 81) {
                event.preventDefault();
                this.$refs.impuestoRef.focus();
            }
            if (event.shiftKey && event.keyCode === 87) {
                event.preventDefault();
                this.$refs.serieComprobanteRef.focus();
            }
            if (event.shiftKey && event.keyCode === 69) {
                event.preventDefault();
                this.$refs.numeroComprobanteRef.focus();
            }
            if (event.shiftKey && event.keyCode === 82) {
                event.preventDefault();
                this.$refs.articuloRef.focus();
            }
            if (event.shiftKey && event.keyCode === 84) {
                event.preventDefault();
                this.$refs.precioRef.focus();
            }
            if (event.shiftKey && event.keyCode === 89) {
                event.preventDefault();
                this.$refs.cantidadRef.focus();
            }
            if (event.shiftKey && event.keyCode === 85) {
                event.preventDefault();
                this.$refs.descuentoRef.focus();
            }
        },

        actualizarDetalle(index) {
            this.arrayDetalle[index].total = (this.arrayDetalle[index].precioseleccionado * this.arrayDetalle[index].cantidad).toFixed(2);
        },
        actualizarDetalleDescuento(index) {
            this.calcularTotal(index);
        },
        validarDescuentoAdicional() {
            if (this.descuentoAdicional >= this.totalParcial) {
                this.descuentoAdicional = 0;
                alert("El descuento adicional no puede ser mayor o igual al total.");
            }
        }, 
        validarDescuentoGiftCard() {
            
            if (this.descuentoGiftCard >= this.calcularTotal) {
                this.descuentoGiftCard = 0;
                alert("El descuento Gift Card no puede ser mayor o igual al total.");
            }
        }, 

        habilitarNombreCliente() {
            if (this.casosEspeciales) {
                this.$refs.documentoRef.setAttribute("readonly", true);
                this.documento = "99001";
                //this.idcliente = "2";
                this.tipo_documento = "5"; 
            } else {
                this.$refs.documentoRef.removeAttribute("readonly");
                this.documento = "";
                //this.idcliente = "";
                this.tipo_documento = "";
            }
        },

        verificarComunicacion() {
            axios.post('/venta/verificarComunicacion')
                .then(function (response) {
                    if (response.data.RespuestaComunicacion.transaccion === true) {
                        document.getElementById("comunicacionSiat").innerHTML = response.data.RespuestaComunicacion.mensajesList.descripcion;
                        document.getElementById("comunicacionSiat").className = "badge bg-success";
                        // Actualiza el valor de scuis
                        //this.scuis = response.data.scuis;
                    } else {
                        document.getElementById("comunicacionSiat").innerHTML = "Desconectado";
                        document.getElementById("comunicacionSiat").className = "badge bg-secondary";
                        // Actualiza el valor de scuis
                        //this.scuis = "Inexistente";
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        },

        cuis() {
            axios.post('/venta/cuis')
                .then(function (response) {
                    if (response.data.RespuestaCuis.transaccion === false) {
                        document.getElementById("cuis").innerHTML = "CUIS: " + response.data.RespuestaCuis.codigo;
                        document.getElementById("cuis").className = "badge bg-primary";
                    } else {
                        document.getElementById("cuis").innerHTML = "CUIS: Inexistente";
                        document.getElementById("cuis").className = "badge bg-secondary";
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        cufd() {
            axios.post('/venta/cufd')
                .then(function (response) {
                    console.log("Respuesta Cufd: " + response.data);
                    if (response.data.transaccion != false) {
                        document.getElementById("cufd").innerHTML = "CUFD vigente: " + response.data.fechaVigencia.substring(0, 16);
                        document.getElementById("direccion").innerHTML = response.data.direccion;
                        document.getElementById("cufdValor").innerHTML = response.data.codigo;
                        document.getElementById("cufd").className = "badge bg-info";
                    } else {
                        document.getElementById("cufd").innerHTML = "No existe CUFD vigente";
                        document.getElementById("cufd").className = "badge bg-secondary";
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        },

        nextNumber() {
            if (!this.num_comprob || this.num_comprob === "") {
                this.last_comprobante++;
                // Completa con ceros a la izquierda hasta alcanzar 5 dígitos
                this.num_comprob = this.last_comprobante.toString().padStart(5, "0");
            }
        },

        listarVenta(page, buscar, criterio) {
            let me = this;
            var url = '/venta2?page=' + page + '&buscar=' + buscar + '&criterio=' + criterio;
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                me.arrayVenta = respuesta.ventas.data;
                me.pagination = respuesta.pagination;
                console.log('lista: ', me.arrayVenta);

            })
                .catch(function (error) {
                    console.log(error);
                });
        },

        selectCliente(search, loading) {
            let me = this;
            loading(true)
            var url = '/cliente/selectCliente?filtro=' + search;
            axios.get(url).then(function (response) {
                //console.log(response.clientes);
                let respuesta = response.data;
                q: search
                me.arrayCliente = respuesta.clientes;
                loading(false)
            })
                .catch(function (error) {
                    console.log(error);
                });
        },
        getDatosCliente(val1) {
            let me = this;
            me.loading = true;
            me.idcliente = val1.id;
            //console.log(val1);
            this.email = val1.email;
            this.nombreCliente = val1.nombre;
            this.documento = val1.num_documento;
            this.tipo_documento = val1.tipo_documento;
            this.complemento_id = val1.complemento_id;

        },

        aplicarDescuento() {
            const descuentoGiftCard = this.descuentoGiftCard;
            const numeroTarjeta = this.numeroTarjeta;
            let idtipo_pago;

            if (numeroTarjeta && descuentoGiftCard) {
                idtipo_pago = 86;
            } else if (numeroTarjeta && !descuentoGiftCard) {
                idtipo_pago = 10;
            } else {
                idtipo_pago = descuentoGiftCard ? 35 : 1;
            }

            this.registrarVenta(idtipo_pago);
        },

        aplicarCombinacion() {
            const descuentoGiftCard = this.descuentoGiftCard
            const idtipo_pago = descuentoGiftCard ? 40 : 2; 

            this.registrarVenta(idtipo_pago);
        },

        otroMetodo(metodoPago){
            const idtipo_pago = metodoPago;
            this.registrarVenta(idtipo_pago);
        },

        buscarArticulo() {
            let me = this;
            var url = '/articulo/buscarArticuloVenta?filtro=' + me.codigo;

            axios.get(url).then(function (response) {
                var respuesta = response.data;
                console.log(respuesta);
                me.arrayArticulo = respuesta.articulos;

                if (me.arrayArticulo.length > 0) {
                    me.articulo = me.arrayArticulo[0]['nombre'];
                    me.codigoComida = me.arrayArticulo[0]['codigo'];
                    me.codigo = me.arrayArticulo[0]['codigo'];
                    me.precio = me.arrayArticulo[0]['precio_venta'];
                    me.stock = me.arrayArticulo[0]['stock'];
                    me.medida = me.arrayArticulo[0]['medida'];
                    me.codigoProductoSin = me.arrayArticulo[0]['codigoProductoSin'];
                }
                else {
                    me.articulo = 'No existe este articulo';
                    me.codigoComida = 0;
                }
            })
                .catch(function (error) {
                    console.log(error);
                });
        },

        abrirTipoVenta() {
                this.modal2 = 1;
                //this.cliente = this.nombreCliente;
                this.tipoAccion2 = 1;
                this.scrollToTop()
        },

        imprimirTicket(id) {
            axios.get('/venta/imprimirTicket/' + id)
                .then(function(response) {
                    const fileURL = response.data.url;
                    const newWindow = window.open(fileURL, '_blank');
                    if (newWindow) {
                        newWindow.focus();
                    } else {
                        console.log("No se pudo abrir una nueva pestaña, asegúrate de que los pop-ups no están bloqueados.");
                    }
                    console.log("Se generó el Ticket correctamente");
                })
                .catch(function(error) {
                    console.log(error);
                });
        },

        cambiarPagina(page, buscar, criterio) {
            let me = this;
            //Actualiza la página actual
            me.pagination.current_page = page;
            //Envia la petición para visualizar la data de esa página
            me.listarVenta(page, buscar, criterio);
        },
        cambiarPaginaA(page, buscar, criterio) {
            let me = this;
            //Actualiza la página actual
            me.pagination.current_page = page;
            //Envia la petición para visualizar la data de esa página
            me.listarArticulo(page, buscar, criterio);
        },
        encuentra(id) {
            var sw = 0;
            for (var i = 0; i < this.arrayDetalle.length; i++) {
                if (this.arrayDetalle[i].codigoComida == id) {
                    sw = true;
                }
            }
            return sw;
        },
        eliminarDetalle(index) {
            let me = this;
            me.arrayDetalle.splice(index, 1);
        },
        agregarDetalle() {
            let me = this;

            let actividadEconomica = 749000;
            let codigoProductoSin = document.getElementById("codigoProductoSin").value;
            let codigoProducto = document.getElementById("codigo").value;
            let descripcion = document.getElementById("nombre_producto").value;
            let cantidad = document.getElementById("cantidad").value;
            let unidadMedida = 57;
            let precioUnitario = document.getElementById("precio").value;
            let montoDescuento = document.getElementById("descuento").value;
            let subTotalValor = document.getElementById("sTotal");
            let subTotal = subTotalValor.textContent;
            let numeroSerie = null;
            let numeroImei = null;


            if (me.codigoComida == 0 || me.cantidad == 0 || me.precio == 0) {

            } else {
                if (me.encuentra(me.codigoComida)) {
                    swal({
                        type: 'error',
                        title: 'Error...',
                        text: 'Este Artículo ya se encuentra agregado!',
                    })
                } else {
                    if (me.cantidad > me.stock) {
                        swal({
                            type: 'error',
                            title: 'Error...',
                            text: 'No hay stock disponible!',
                        })
                    } else {
                        me.arrayDetalle.push({
                            codigoComida: me.codigoComida,
                            articulo: me.articulo,
                            medida: me.medida,
                            cantidad: me.cantidad,
                            precio: me.precio,
                            descuento: me.descuento,
                            stock: me.stock,
                        });
                        console.log("Estoy entrando s arraydetalle")

                        me.arrayFactura.push({
                            actividadEconomica: actividadEconomica,
                            codigoProductoSin: codigoProductoSin,
                            codigoProducto: codigoProducto,
                            descripcion: descripcion,
                            cantidad: cantidad,
                            unidadMedida: unidadMedida,
                            precioUnitario: precioUnitario,
                            montoDescuento: montoDescuento,
                            subTotal: subTotal,
                            numeroSerie: numeroSerie,
                            numeroImei: numeroImei
                        });

                        me.codigo = '';
                        me.codigoComida = 0;
                        me.articulo = '';
                        me.medida = '';
                        me.cantidad = 0;
                        me.precio = 0;
                        me.descuento = 0;
                        me.stock = 0;
                        me.sTotal = 0;
                    }
                }

            }

        },
        agregarDetalleModal(data = []) {
            let me = this;

            let actividadEconomica = 749000;
            //let codigoProductoSin = document.getElementById("codigoProductoSin").value;
            //let codigoProducto = document.getElementById("codigo").value;
            //let descripcion = document.getElementById("nombre_producto").value;
            //let cantidad = document.getElementById("cantidad").value;
            let unidadMedida = 57;
            //let precioUnitario = document.getElementById("precio").value;
            //let montoDescuento = document.getElementById("descuento").value;
            let montoDescuento = 0;
            //let subTotalValor = document.getElementById("sTotal");
            //let subTotal = subTotalValor.textContent;
            let numeroSerie = null;
            let numeroImei = null;
            //let descuento = ((this.precioseleccionado * this.cantidad) * (this.descuentoProducto / 100)).toFixed(2);

            
            if (me.encuentra(data['codigo'])) {
                swal({
                    type: 'error',
                    title: 'Error...',
                    text: 'Este Artículo ya se encuentra agregado!',
                })
            } else {
                me.arrayDetalle.push({
                    codigoComida: data['codigo'],
                    articulo: data['nombre'],
                    cantidad: 1,
                    precio: data['precio_venta'],
                    descuento: 0,
                    stock: data['stock'],
                    medida: data['medida'],
                });
                console.log("ArrayDetalle:" + me.arrayDetalle);
                me.arrayFactura.push({
                            actividadEconomica: actividadEconomica,
                            codigoProductoSin: data['codigoProductoSin'],
                            codigoProducto: data['codigo'],
                            descripcion: data['nombre'],
                            cantidad: 1,
                            unidadMedida: unidadMedida,
                            precioUnitario: data['precio_venta'],
                            montoDescuento: montoDescuento,
                            subTotal: data['precio_venta'],
                            numeroSerie: numeroSerie,
                            numeroImei: numeroImei
                        });
                        console.log("Para la Factura: " + me.arrayFactura);
            }
        },

        agregarProductos(id) {
            this.ventaSeleccionada = id;
            console.log("El id de la reserva es: " + this.ventaSeleccionada);
            this.cambiarTipoProducto(); // Llama a la función para listar los productos basados en el tipo seleccionado
            $('#productModal').modal('show');
        },

        guardarProductosSeleccionados() {
        const productosSeleccionados = this.arrayProductos.filter(producto => producto.seleccionado && producto.cantidad > 0);

        const requests = productosSeleccionados.map(producto => {
            return axios.post('/venta/guardarProducto', {
                idventa: this.ventaSeleccionada,
                codigoComida: producto.codigo,
                cantidad: producto.cantidad,
                precio: producto.precio_venta
            });
        });

        Promise.all(requests)
            .then(responses => {
                console.log('Productos guardados:', responses);
                this.listarVenta(1, '', '');
                swal(
                    'PRODUCTOS GUARDADOS EXITÓSAMENTE',
                    'Todos los productos han sido guardados con éxito.',
                    'success'
                );

                // Limpiar los campos de cantidad y deseleccionar los checkboxes
                this.arrayProductos.forEach(producto => {
                    producto.cantidad = 0;
                    producto.seleccionado = false;
                });

                $('#productModal').modal('hide');
            })
            .catch(error => {
                console.error('Error al guardar los productos:', error);
                swal(
                    'ERROR AL GUARDAR LOS PRODUCTOS',
                    'Hubo un error al intentar guardar los productos.',
                    'error'
                );
            });
    },

        listarProducto2(){
                let me = this;
                axios.get('/articulo/listarSinRepetir').then(function(response) {
                var respuesta = response.data;
                me.arrayProductos = respuesta.articulos;
                }).catch(error => {
                console.error('Error al cargar productos:', error);
            });
            },
        
        listarMenu2(){
                let me = this;
                axios.get('/menu/listarSinRepetir').then(function(response) {
                var respuesta = response.data;
                me.arrayProductos = respuesta.articulos;
                }).catch(error => {
                console.error('Error al cargar productos:', error);
            });
        },

        cambiarTipoProducto() {
            if (this.selectedProductType === 'menu') {
                this.listarMenu2();
            } else {
                this.listarProducto2();
            }
        },

        actualizarArrayProductos(index) {
            let detalle = this.arrayDetalle[index];
            let producto = this.arrayFactura[index];

            producto.cantidad = detalle.cantidad;
            producto.subTotal = detalle.cantidad * producto.precioUnitario;

        },

        listarArticulo(page, criterioA) {
            let me = this;
            var url = '/articulo/listarArticuloVenta?page=' + page + '&criterio='+ criterioA + '&idAlmacen='+ this.idAlmacen;
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                console.log(respuesta);
                me.arrayArticulo = respuesta.articulos.data;
                me.pagination = respuesta.pagination;
            })
                .catch(function (error) {
                    console.log(error);
                });
        },

        listarMenu() {
            let me = this;
            var url = '/menu/getAllMenu';
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                me.arrayMenu.splice(0, me.arrayMenu.length);
                me.arrayMenu = respuesta.articulos;
                me.pagination = respuesta.pagination;
                console.log('lista menu -comida: ', me.arrayMenu);
            })
            .catch(function (error) {
                console.log(error);
            });
        },

        listarProducto(page, buscar, criterio) {
            let me = this;
            var url = '/articulo?page=' + page + '&buscar=' + buscar + '&criterio=' + criterio;
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                me.arrayMenu.splice(0, me.arrayMenu.length);
                me.arrayMenu = respuesta.articulos.data;
                me.pagination = respuesta.pagination;
                console.log("lista menu -bebida: ", me.arrayMenu);
            })
                .catch(function (error) {
                    console.log(error);
                });
        },

        async obtenerDatosUsuario() {
            try {
                const response = await axios.get('/venta');
                this.idusuario = response.data.usuario.id;
                console.log("El id usuario es: " + this.idusuario);
                this.usuarioAutenticado = response.data.usuario.usuario;
                this.usuario_autenticado = this.usuarioAutenticado;
                this.idrol = response.data.usuario.idrol;
                this.idsucursalusuario = response.data.usuario.idsucursal;
                this.id_sucursal_actual = response.data.usuario.idsucursal;
                this.puntoVentaAutenticado = response.data.codigoPuntoVenta;

                //this.listarMenu(this.id_sucursal_actual);
            } catch (error) {
                console.error(error);
            }
        },
        

        /*async verificarFactura(id) {
            try {
                const response = await axios.get(`/verificarFactura/${id}`);
                console.log(response);
                const { cuf, numeroFactura } = response.data;

                if (cuf && numeroFactura) {
                    const url = `https://pilotosiat.impuestos.gob.bo/consulta/QR?nit=5926531015&cuf=${cuf}&numero=${numeroFactura}&t=2`;
                    window.open(url);
                } else {
                    swal(
                        'DATOS DE LA FACTURA NO ENCONTRADOS',
                        'No se realizó el pago',
                        'warning'
                    );
                    console.error('Datos de la factura no encontrados.');
                }
            } catch (error) {
                console.error('Error al verificar la factura:', error);
                swal(
                        'DATOS DE LA FACTURA NO ENCONTRADOS',
                        'No se realizó el pago',
                        'warning'
            );
            }
        },*/

        verificarFactura(cuf, numeroFactura){
            var url = 'https://pilotosiat.impuestos.gob.bo/consulta/QR?nit=5153610012&cuf='+cuf+'&numero='+numeroFactura+'&t=2';
            window.open(url);        
        },

        anularFactura(id, cuf) {
            swal({
                title: '¿Está seguro de anular la factura?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                let me = this;
                axios.get('/factura/obtenerDatosMotivoAnulacion')
                    .then(function(response) {
                    var respuesta = response.data;
                    me.arrayMotivosAnulacion = respuesta.motivo_anulaciones;
                    
                    console.log('Motivos obtenidos:', me.arrayMotivosAnulacion);

                    let options = {};
                    me.arrayMotivosAnulacion.forEach(function(motivo) {
                        options[motivo.codigo] = motivo.descripcion;
                    });

                    // Muestra un segundo modal para seleccionar el motivo
                    swal({
                        title: 'Seleccione un motivo de anulación',
                        input: 'select',
                        inputOptions: options,
                        inputPlaceholder: 'Seleccione un motivo',
                        showCancelButton: true,
                        inputValidator: function (value) {
                        return new Promise(function (resolve, reject) {
                            if (value !== '') {
                            resolve();
                            } else {
                            reject('Debe seleccionar un motivo');
                            }
                        });
                        }
                    }).then((result) => {
                        if (result.value) {
                        // Aquí obtienes el motivo seleccionado y puedes realizar la solicitud para anular la factura
                        const motivoSeleccionado = result.value;
                        //axios.get('/factura/anular2/' + id +"/" + motivoSeleccionado)
                        axios.get('/factura/anular/' + cuf +"/" + motivoSeleccionado)

                            .then(function(response) {
                            const data = response.data;
                            if (data === 'ANULACION CONFIRMADA') {
                                swal(
                                'FACTURA ANULADA',
                                data,
                                'success'
                                );
                            } else {
                                swal(
                                'ANULACION RECHAZADA',
                                data,
                                'warning'
                                );
                            }
                            })
                            .catch(function(error) {
                            console.log(error);
                            });
                        }
                    });
                    })
                    .catch(function(error) {
                    console.log(error);
                    });
                }
            });
            },

            /*imprimirFactura(id, correo) {
            swal({
                title: 'Selecciona un tamaño de factura',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'CARTA',
                cancelButtonText: 'ROLLO',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    console.log("El boton CARTA fue presionado");
                    //axios.get('/factura/imprimirCarta2/' + id, { responseType: 'blob' })
                    axios.get('/factura/imprimirCarta/' + id + '/' + correo, { responseType: 'blob' })
                        .then(function (response) {
                            window.location.href = "docs/facturaCarta.pdf";
                            console.log("Se generó el factura en Carta correctamente");
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                } else if (result.dismiss === swal.DismissReason.cancel) {
                    console.log("El boton ROLLO fue presionado");
                    //axios.get('/factura/imprimirRollo2/' + id, { responseType: 'blob' })
                    axios.get('/factura/imprimirRollo/' + id + '/' + correo, { responseType: 'blob' })

                        .then(function (response) {
                            window.location.href = "docs/facturaRollo.pdf";
                            console.log("Se generó el la factura en Rollo correctamente");
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                }
            }).catch((error) => {
                console.error('Error al mostrar el diálogo:', error);
            });
        },*/

        imprimirFactura(id) {
            axios.get('/factura/imprimirRollo/' + id)
                .then(function(response) {
                    const fileURL = response.data.url;
                    const newWindow = window.open(fileURL, '_blank');
                    if (newWindow) {
                        newWindow.focus();
                    } else {
                        console.log("No se pudo abrir una nueva pestaña, asegúrate de que los pop-ups no están bloqueados.");
                    }
                    console.log("Se generó la factura en Rollo correctamente");
                })
                .catch(function(error) {
                    console.log(error);
                });
        },
        
        selectAlmacen() {
            let me = this;
            var url = '/almacen/selectAlmacen';
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                me.arrayAlmacenes = respuesta.almacenes;
                console.log(me.arrayAlmacenes);

            })
                .catch(function (error) {
                    console.log(error);
                });
        },
        getAlmacenProductos(almacen) {
            let me = this;
            me.idAlmacen = 1;
            console.log(me.idAlmacen);
        },
        validarVenta() {
            let me = this;
            me.errorVenta = 0;
            me.errorMostrarMsjVenta = [];
            var art;

            me.arrayDetalle.map(function (x) {
                if (x.cantidad > x.stock) {
                    art = x.articulo + " Stock insuficiente";
                    me.errorMostrarMsjVenta.push(art);
                }
            });

            if (!me.cliente) me.errorMostrarMsjVenta.push("Ingrese el Nombre de un Cliente");
            if (me.tipo_comprobante == 0) me.errorMostrarMsjVenta.push("Seleccione el Comprobante");
            if (!me.impuesto) me.errorMostrarMsjVenta.push("Ingrese el impuesto de compra");
            if (me.arrayDetalle.length <= 0) me.errorMostrarMsjVenta.push("Ingrese detalles");

            if (me.errorMostrarMsjVenta.length) me.errorVenta = 1;

            return me.errorVenta;
        },

        aplicarDescuento() {
            const descuentoGiftCard = this.descuentoGiftCard;
            const numeroTarjeta = this.numeroTarjeta;
            let idtipo_pago;

            if (numeroTarjeta && descuentoGiftCard) {
                idtipo_pago = 86;
            } else if (numeroTarjeta && !descuentoGiftCard) {
                idtipo_pago = 10;
            } else {
                idtipo_pago = descuentoGiftCard ? 35 : 1;
            }

            this.registrarVenta(idtipo_pago);
        },

        aplicarCombinacion() {
            const descuentoGiftCard = this.descuentoGiftCard
            const idtipo_pago = descuentoGiftCard ? 40 : 2; 

            this.registrarVenta(idtipo_pago);
        },

        otroMetodo(metodoPago){
            const idtipo_pago = metodoPago;
            this.registrarVenta(idtipo_pago);
                },


                //-------------REGISTRAR VENTA ------
                async registrarVenta(idtipo_pago) {
                /*if (this.validarVenta()) {
                    return;
                }*/

                let me = this;
                let idvent = this.idventaa;
                this.idtipo_pago = idtipo_pago;

                try {
                    const response = await axios.get(`/api/clientes/existe?documento=${this.documento}`);
                    if (!response.data.existe) {
                        const nuevoClienteResponse = await axios.post('/cliente/registrar', {
                            'nombre': this.cliente,
                            'num_documento': this.documento,
                            'email': this.email
                        });

                        this.idcliente = nuevoClienteResponse.data.id;
                    } else {
                        this.idcliente = response.data.cliente.id;
                    }

                    axios.put('/venta/cerrarVenta', {
                    'id': idvent,
                    'idcliente': this.idcliente,
                    'tipo_comprobante': "FACTURA",
                    'serie_comprobante': this.serie_comprobante,
                    'num_comprobante': this.num_comprob,
                    'impuesto': this.impuesto,
                    'idtipo_pago': idtipo_pago,
                    'idtipo_venta': this.idtipo_venta,
                    'cliente': this.cliente,
                    'documento': this.documento,
                    'observacion': this.observacion,
                    'estado': "1"

                }).then(function (response) {
                    const ventaId = response.data.id;
                    const detalles = response.data.detalles;
                    console.log('Venta cerrada con ID:', ventaId);

                    detalles.forEach(data => {
                        me.arrayFactura.push({
                            actividadEconomica: data.actividadEconomica,
                            codigoProductoSin: data.codigoProductoSin,
                            codigoProducto: data.codigo,
                            descripcion: data.nombre,
                            cantidad: data.cantidad,
                            unidadMedida: 57,
                            precioUnitario: data.precio_venta,
                            montoDescuento: data.montoDescuento,
                            subTotal: data.precio_venta * data.cantidad,
                            numeroSerie: null,
                            numeroImei: null 
                        });
                    });

                    console.log("Para la Factura: ", me.arrayFactura);

                    swal(
                        'VENTA CERRADA',
                        'Éxito',
                        'success'
                    );
                        me.emitirFactura(ventaId);
                        me.ejecutarFlujoCompleto();
                        me.listado = 1;
                        me.listarVenta(1, '', 'num_comprob');
                        me.cerrarModal2();
                        me.idproveedor = 0;
                        me.tipo_comprobante = 'FACTURA';
                        me.categoria_general = 'bebidas'
                        me.idtipo_pago = '';
                        me.email = '';
                        me.numeroTarjeta =  null;
                        me.metodoPago = '';
                        me.menu = 49;
                        me.idproveedor = 0;
                        me.nombreCliente = '';
                        me.idcliente = '';
                        me.tipo_documento = 0;
                        me.complemento_id = '';
                        me.cliente = '';
                        me.documento = '';
                        me.email = '';
                        me.imagen = '';
                        me.serie_comprobante = '';
                        me.impuesto = 0.18;
                        me.total = 0.0;
                        me.codigoComida = 0;
                        me.articulo = '';
                        me.cantidad = 0;
                        me.precio = 0;
                        me.stock = 0;
                        me.codigo = '';
                        me.descuento = 0;
                        me.arrayDetalle = [];
                        me.primer_precio_cuota = 0;
                        me.recibido = 0;
                        $('#paymentModal').modal('hide');

                }).catch(function (error) {
                    console.log(error);
                    swal(
                        'FALLO AL CERRAR LA VENTA',
                        'Intente de Nuevo',
                        'warning'
                    );
                    $('#paymentModal').modal('hide');
                });
                }catch (error) {
                    console.error('Error al cerrar la venta:', error);
                } 
            },

        async emitirFactura(ventaId) {
        console.log("Se paso a emitir Factura");

        let me = this;

        let idventa = ventaId;
        //let numeroFactura = document.getElementById("num_comprobante").value;
        let numeroFacturaPrueba = String(this.num_factura);
        let numeroFactura = numeroFacturaPrueba.padStart(5, '0');
        let cuf = "464646464";
        let cufdValor = document.getElementById("cufdValor");
        console.log("hola aaaa: ", this.cufdValor);
        let numeroTarjeta = this.numeroTarjeta;

        let cufd = cufdValor.textContent;
        let direccionValor = document.getElementById("direccion");
        let direccion = direccionValor.textContent;
        var tzoffset = (new Date()).getTimezoneOffset() * 60000;
        let fechaEmision = (new Date(Date.now() - tzoffset)).toISOString().slice(0, -1);
        //let id_cliente = document.getElementById("idcliente").value;
        //let nombreRazonSocial = document.getElementById("cliente").value;
        let nombreRazonSocial = this.cliente;
        //let numeroDocumento = document.getElementById("documento").value;
        let numeroDocumento = this.documento;
        //let complemento = document.getElementById("complemento_id").value;
        let complemento = null;
        //let tipoDocumentoIdentidad = document.getElementById("tipo_documento").value;
        let tipoDocumentoIdentidad = 5;
        //let montoTotal = (this.totalReservaSeleccionada.toFixed(2));
        let montoTotal = Number(this.totalReservaSeleccionada).toFixed(2);
        //let descuentoAdicional = document.getElementById("descuentoAdicional").value;
        let descuentoAdicional = this.descuentoAdicional;
        //let usuario = document.getElementById("usuarioAutenticado").value;
        let usuario = this.usuarioAutenticado;
        //let codigoPuntoVenta = document.getElementById("puntoVentaAutenticado").value;
        let codigoPuntoVenta = this.puntoVentaAutenticado;
        //let montoGiftCard = document.getElementById("descuentoGiftCard").value;
        let codigoMetodoPago = this.idtipo_pago;
        let montoTotalSujetoIva = montoTotal - this.descuentoGiftCard;
        //let correo = document.getElementById("email").value;
        let correo = this.email;

        console.log("El monto de Descuento de Gift Card es: " + this.descuentoGiftCard);
        console.log("El tipo de documento es: " + tipoDocumentoIdentidad);
        console.log("El complemento de documento es: " + complemento);
        console.log("hola monto toal: " + this.calcularTotal.toFixed(2));

        try {
            const response = await axios.get('/factura/obtenerLeyendaAleatoria');
            this.leyendaAl = response.data.descripcionLeyenda;
            console.log("El dato de leyenda llegado es: " + this.leyendaAl);
        } catch (error) {
            console.error(error);
            return '"Ley N° 453: Los servicios deben suministrarse en condiciones de inocuidad, calidad y seguridad."';
        }

        try {
                if (tipoDocumentoIdentidad === 5) {
                    const response = await axios.post('/factura/verificarNit/' + numeroDocumento);
                    if (response.data === 'NIT ACTIVO') {
                        me.codigoExcepcion = 0;
                        //alert("NIT VÁLIDO.");
                    } else {
                        me.codigoExcepcion = 1;
                        //alert("NIT INVÁLIDO.");
                    }
                }else{
                    me.codigoExcepcion = 0;
                }
            } catch (error) {
                console.error(error);
                return 'No se pudo verificar el NIT';
            }

        var factura = [];
        factura.push({
            cabecera: {
                nitEmisor: "5153610012",
                razonSocialEmisor: "SAMMY KEVIN DE LA ZERDA BUSTAMANTE",
                municipio: "Cochabamba",
                telefono: "77777777",
                numeroFactura: numeroFactura,
                cuf: cuf,
                cufd: cufd,
                codigoSucursal: 0,
                direccion: direccion,
                codigoPuntoVenta: codigoPuntoVenta,
                fechaEmision: fechaEmision,
                nombreRazonSocial: nombreRazonSocial,
                codigoTipoDocumentoIdentidad: tipoDocumentoIdentidad,
                numeroDocumento: numeroDocumento,
                complemento: complemento,
                codigoCliente: numeroDocumento,
                codigoMetodoPago: codigoMetodoPago,
                numeroTarjeta: numeroTarjeta,
                montoTotal: montoTotal,
                montoTotalSujetoIva: montoTotalSujetoIva,
                codigoMoneda: 1,
                tipoCambio: 1,
                montoTotalMoneda: montoTotal,
                montoGiftCard: this.descuentoGiftCard,
                descuentoAdicional: descuentoAdicional,
                codigoExcepcion: this.codigoExcepcion,
                cafc: null,
                leyenda: this.leyendaAl,
                usuario: usuario,
                codigoDocumentoSector: 1
            }
        })
        me.arrayFactura.forEach(function (prod) {
            factura.push({ detalle: prod })
        })

        var datos = { factura };

        axios.post('/venta/emitirFactura', {
            factura: datos,
            //id_cliente: id_cliente,
            idventa: idventa,
            correo: correo,
            cufd: cufd
        })
            .then(function (response) {
                var data = response.data;
                var mensaje = data.mensaje;
                var idFactura = data.idFactura;

                console.log(data);

                if (mensaje === "VALIDADA") {
                    swal(
                        'FACTURA VALIDADA',
                        'Correctamente',
                        'success'
                    )
                    //me.imprimirTicket(idventa);
                    me.imprimirFactura(idFactura, correo);

                    me.arrayFactura = [];
                    me.codigoExcepcion = 0;
                    me.idtipo_pago = '';
                    me.email = '';
                    me.descuentoGiftCard = '';
                    me.numeroTarjeta =  null;
                    me.recibido = '';
                    me.metodoPago = '';
                    me.cerrarModal2();
                    me.listarVenta(1, '', 'id');
                    me.mostrarSpinner = false;
                } else{
                    me.arrayFactura = [];
                    me.codigoExcepcion = 0;
                    me.idtipo_pago = '';
                    me.descuentoGiftCard = '';
                    me.numeroTarjeta =  null;
                    me.recibido = '';
                    me.metodoPago = '';
                    me.last_comprobante = '';
                    me.cerrarModal2();
                    me.listarVenta(1, '', 'id');
                    me.mostrarSpinner = false;
                    swal(
                        'FACTURA RECHAZADA',
                        data,
                        'warning'
                    );
                    me.eliminarVenta(idVentaRecienRegistrada);
                }
            })
            .catch(function (error) {
                console.error(error);
                me.arrayFactura = [];
                me.codigoExcepcion = 0;
                swal(
                    'INTENTE DE NUEVO',
                    'Comunicacion con SIAT fallida',
                    'error');
                me.mostrarSpinner = false;
                me.idtipo_pago = '';
                me.numeroTarjeta =  null;
                me.descuentoGiftCard = '';
                me.recibido = '';
                me.metodoPago = '';
            });
        },

        eliminarVenta(idVenta) {
            axios.delete('/venta/eliminarVenta/' + idVenta)
                .then(function (response) {
                    console.log('Venta eliminada correctamente:', response);
                })
                .catch(function (error) {
                    console.error('Error al eliminar la venta:', error);
                });
        },

        getEstadoText(estado) {
            if (estado === '1') {
                return 'Pagado';
            } else if (estado === '0') {
                return 'Cancelado';
            } else if (estado === '2') {
                return 'Pendiente a Pago';
            } else {
                return 'Desconocido'; // en caso de un estado inesperado
            }
        },

        getEstadoClass(estado) {
            return {
                'text-green': estado === "1",
                'text-red': estado === "0",
                'text-orange': estado === "2" // color naranja para pendiente a pago, por ejemplo
            };
        },

        mostrarDetalle() {
            let me = this;
            me.selectAlmacen();
            me.listado = 0;
            
            me.nombreCliente = '';
            me.idcliente = 0;
            me.tipo_documento = '';
            me.complemento_id = '';
            me.documento = '';
            me.email = '';
            me.cafc = '';
            me.idproveedor = 0;
            me.tipo_comprobante = 'TICKET';
            me.serie_comprobante = '';
            me.nextNumber();
            //me.num_comprobante = '';
            me.impuesto = 0.18;
            me.total = 0.0;
            me.codigoComida = 0;
            me.articulo = '';
            me.cantidad = 0;
            me.precio = 0;
            me.arrayDetalle = [];
            this.listarMenu(this.buscar, this.criterio);
        },

        ocultarDetalle() {
            this.listado = 1;
        },

        verVenta(id) {
            let me = this;
            me.listado = 2;

            //Obtener datos del ingreso
            var arrayVentaT = [];
            var url = '/venta/obtenerCabecera?id=' + id;

            axios.get(url).then(function (response) {
                var respuesta = response.data;
                arrayVentaT = respuesta.venta;

                me.cliente = arrayVentaT[0]['nombre'];
                me.tipo_comprobante = arrayVentaT[0]['tipo_comprobante'];
                me.serie_comprobante = arrayVentaT[0]['serie_comprobante'];
                me.num_comprobante = arrayVentaT[0]['num_comprobante'];
                me.impuesto = arrayVentaT[0]['impuesto'];
                me.total = arrayVentaT[0]['total'];
            })
                .catch(function (error) {
                    console.log(error);
                });

            //obtener datos de los detalles
            var url = '/venta/obtenerDetalles?id=' + id;

            axios.get(url).then(function (response) {
                //console.log(response);
                var respuesta = response.data;
                me.arrayDetalle = respuesta.detalles;
                console.log(me.arrayDetalle);
            })
                .catch(function (error) {
                    console.log(error);
                });
        },
        cerrarModal() {
            this.modal = 0;
            this.tituloModal = '';
            me.idproveedor = 0;
                    me.cliente = '';
                    me.tipo_comprobante = 'TICKET';
                    me.serie_comprobante = '';
                    me.num_comprob = '';
                    me.impuesto = 0.18;
                    me.total = 0.0;
                    me.codigoComida = 0;
                    me.articulo = '';
                    me.cantidad = 0;
                    me.precio = 0;
                    me.stock = 0;
                    me.codigo = '';
                    me.descuento = 0;
                    me.arrayDetalle = [];
        },
        cerrarModal2() {
            this.modal2 = 0;
            this.tituloModal2 = '';
            this.idtipo_pago = '';
            this.tipoPago = '';
        },
        abrirModal() {
            if (this.idAlmacen == 0) {
                return;
            }
            //this.listarArticulo();
            this.modal = 1;
            this.tituloModal = 'Seleccione los articulos que desee';

        },

        desactivarVenta(id) {
            swal({
                title: 'Esta seguro de anular esta venta?',
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

                    axios.put('/venta/desactivar', {
                        'id': id
                    }).then(function (response) {
                        me.listarVenta(1, '', 'num_comprobante');
                        swal(
                            'Anulado!',
                            'La venta ha sido anulado con éxito.',
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

        /*abrirModalPago(ventaId) {
            
            $('#paymentModal').modal('show');
        },

        abrirModalPago(ventaId) {
            // Encontrar la venta seleccionada por su ID
            const ventaSeleccionada = ventaId;
            console.log("hola: ", ventaSeleccionada);

            if (ventaSeleccionada) {
                // Establecer el total de la venta seleccionada
                this.totalReservaSeleccionada = ventaSeleccionada.total;

                // Mostrar el modal de pago
                $('#paymentModal').modal('show');
            } else {
                console.error('Venta no encontrada');
            }
        },*/

        abrirModalPago(ventaId) {
            this.idventaa = ventaId;
            axios.get(`/ventaselect/${ventaId}`)
                .then(response => {
                    const ventaSeleccionada = response.data;
                    console.log("Venta seleccionada: ", ventaSeleccionada);

                    if (ventaSeleccionada) {
                        // Establecer los datos de la venta seleccionada
                        this.totalReservaSeleccionada = ventaSeleccionada.total;
                        //this.num_comprob = ventaSeleccionada.num_comprobante;

                        // Mostrar el modal de pago
                        $('#paymentModal').modal('show');
                    } else {
                        console.error('Venta no encontrada');
                    }
                })
                .catch(error => {
                    console.error('Error al obtener la venta:', error);
                });
        },

        async fetchClienteData() {
            if (this.documento) {
                try {
                    const response = await axios.get(`/api/clientes?documento=${this.documento}`);
                    if (response.data.success) {
                        this.cliente = response.data.cliente.nombre;
                        this.email = response.data.cliente.email;
                    } else {
                        alert('Cliente no encontrado');
                        this.cliente = '';
                        this.email = '';
                    }
                } catch (error) {
                    console.error('Error al buscar los datos del cliente:', error);
                }
            }
        },

        async obtenerDatosSesionYComprobante() {
            console.log("El id usuario en comprobante es: " + this.idusuario);
            try {
                const sesionResponse = await axios.get('/obtener-datos-sesion');
                this.scodigorecepcion = sesionResponse.data.scodigorecepcion;
                console.log('Valor de scodigorecepcion:', this.scodigorecepcion);

                const idsucursal = this.idusuario === 1 ? this.sucursalSeleccionada : this.idsucursalusuario;
                console.log("El id sucursal en comprobante es: " + idsucursal);
                const comprobanteResponse = await axios.get('/obtener-ultimo-comprobante', {
                    params: {
                        idsucursal: idsucursal
                    }
                });
                console.log(comprobanteResponse.data.next_comprobante);
                this.num_comprob = comprobanteResponse.data.next_comprobante;
                console.log('Next comprobante:', this.num_comprob);
            } catch (error) {
                console.error('Error al obtener datos de sesión o el último comprobante:', error);
            }
        },

        async ejecutarFlujoCompleto() {
            await this.obtenerDatosUsuario();
            await this.obtenerDatosSesionYComprobante();
        },

        async obtenerNumeroFactura() {
            try {
                const response = await axios.get('/facturas/ultimo-numero');
                const ultimoNumero = response.data.ultimoNumero;
                this.num_factura = ultimoNumero + 1; 
            } catch (error) {
                console.error('Error al obtener el último número de factura:', error);
            }
        },   
    },



    created() {
        // Realiza una solicitud AJAX para obtener los datos de sesión
        /*axios.get('/obtener-datos-sesion')
        .then(response => {
            this.scodigorecepcion = response.data.scodigorecepcion;
            console.log('Valor de scodigorecepcion:', this.scodigorecepcion);
            return axios.get('/ruta-a-tu-endpoint-laravel-para-obtener-ultimo-comprobante');
        })
        .then(response => {
            const lastComprobante = response.data.last_comprobante;
            this.last_comprobante = lastComprobante;
            this.nextNumber();
        })
        .catch(error => {
            console.error('Error al obtener datos de sesión o el último comprobante:', error);
        });*/
    },


    mounted() {
        this.listarVenta(1, this.buscar, this.criterio);
        window.addEventListener('keydown', this.atajoButton);
        this.verificarComunicacion();
        this.cuis();
        this.cufd();
        //this.obtenerDatosUsuario();
        //this.listarArticulo(1, this.buscar, this.criterio);

        //this.listarMenu(this.buscar, this.criterio);
        //this.listarProducto(1, this.buscar, this.criterio);
        this.getCategoriasMenu();
        this.getCategoriasProductos();
        this.listarProducto2();

        this.updateButtonStyle();
        window.addEventListener('resize', this.updateButtonStyle);
        this.ejecutarFlujoCompleto();
        this.actualizarFechaHora();
        this.obtenerNumeroFactura();

    },

    beforeDestroy() {
        window.removeEventListener('resize', this.updateButtonStyle);
    }
}

</script>
<style>
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

    .table-warning {
        background-color: yellow;
    }

    .text-green {
        color: green;
        font-weight: bold;
    }

    .text-red {
        color: red;
        font-weight: bold;
    }

    .text-orange {
        color: orange;
        font-weight: bold;
    }

</style>