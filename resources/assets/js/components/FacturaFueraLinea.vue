<template>
        <main class="main">
            <div class="p-p-4 p-mx-auto" style="max-width: 100%;">

            <Panel header="Menu Completo" style="font-size: 1.5rem;" :toggleable="false">
                <span class="badge bg-secondary" id="comunicacionSiat" style="color: white;" v-show="mostrarElementos">Desconectado</span>
                <span class="badge bg-secondary" id="cuis" v-show="mostrarElementos">CUIS: Inexistente</span>
                <span class="badge bg-secondary" id="cufd" v-show="mostrarElementos">No existe cufd vigente</span>
                <span class="badge bg-secondary" id="direccion" v-show="mostrarDireccion">No hay dirección registrada</span>
                <span class="badge bg-primary" id="cufdValor" v-show="mostrarCUFD">No hay CUFD</span>

                <Toast :breakpoints="{'920px': {width: '100%', right: '0', left: '0'}}" style="padding-top: 40px;"></Toast>

                <template #header>
                    <div class=""buttons-container>
                    <SelectButton v-model="subcategoria_seleccionada" :options="lista_subcategorias" dataKey="value">
                        <template #option="slotProps">
                            <div class="p-col-12 p-md-4 custom-button-content">
                            <span>{{ slotProps.option.label }}</span>
                            </div>
                        </template>
                    </SelectButton>
                    </div>
                </template>

                <template #icons>
                    <Button v-if="idrol === 1 && categoria_general === 'bebidas'" class="p-button-sm p-button-raised p-button-primary " @click="toggle_almacen" style="margin-left: 5px;">
                        <span class="pi pi-truck" style="font-size: 1.3rem;"></span>
                    </Button>
                    <Menu v-if="idrol === 1 && categoria_general === 'bebidas'" id="config_almacen" ref="almacen" :model="almacenes_lista" :popup="true" />

                    <Button class="p-button-sm p-button-raised p-button-warning " @click="toggle_menu" style="margin-left: 5px;">
                        <span class="pi pi-tags" style="font-size: 1.3rem;"></span>
                    </Button>
                    <Menu id="config_menu" ref="menu" :model="categorias_lista" :popup="true" />
                </template>

                <template>

                    <div>
                        <DataView :value="arrayMenuFiltrado" layout="grid" :paginator="true" :rows="filas_dinamicas">
                            <template #grid="slotProps">
                                <div class="product-container" style="padding-right: 7px; padding-left: 7px; padding-bottom: 12px;" @click.stop="agregarDetalleModal(slotProps.data)">
                                    <Card
                                        :class="getCardClass(slotProps.data)"
                                        v-tooltip="mostrarInformacionProducto(slotProps.data)"
                                    >
                                        <template #header>
                                            <div class="image-container">
                                                <img :src="'/img/menu/' + slotProps.data.fotografia" alt="Product Image" class="product-image">
                                            </div>
                                        </template>

                                        <template #title>
                                            <div class="product-name">{{ truncateAndCapitalize(slotProps.data.nombre) }}</div>
                                        </template>
                                        
                                        <template #footer>
                                            <div class="footer-content">
                                                <!--<Button label="BORRAR" icon="pi pi-pencil" class="p-button-sm p-button-warning rounded-bottom-right" @click.stop="visibleRight = true" />-->
                                                <Button v-if="slotProps.data.saldo_stock === 0 || slotProps.data.saldo_stock === null" icon="pi pi-times" class="p-button-sm p-button-danger rounded-bottom-right" disabled />
                                                <Button v-else-if="slotProps.data.saldo_stock <= slotProps.data.stockmin" icon="pi pi-bell" class="p-button-sm p-button-warning rounded-bottom-right" disabled />
                                                <Button v-else icon="pi pi-plus" class="p-button-sm p-button-success rounded-bottom-right" disabled />
                                                <div class="price">Bs {{ slotProps.data.precio_venta }}</div>
                                            </div>
                                        </template>
                                    </Card>
                                </div>
                            </template>

                            <template #empty>Menu vacio</template>
                        </DataView>
                    </div>

                <div class="floating-buttons">
                    <div class="button-badge-container">
                        <!--<Button class="p-button-lg p-button-success floating-button" @click.stop="toggleCart">
                            <i class="pi pi-shopping-cart" style="font-size: 3.5rem"></i>
                        </Button>-->
                        <Button class="p-button-lg p-button-success floating-button" @click.stop="toggleCart">
                            <i class="pi pi-shopping-cart" style="font-size: 3.5rem"></i>
                        </Button>
                        <Badge :value="arrayDetalle.length" size="large" severity="danger" class="floating-badge"></Badge>
                    </div>
                </div>


                <Dialog :visible.sync="visibleDialog" :containerStyle="dialogStyle" :modal="true" position="right" :closable="false">
                    <template #header>
                        <div class="sidebar-header">
                            <i class="pi pi-shopping-cart sidebar-icon"></i>
                            <h4 class="sidebar-title">DATOS DE LA VENTA</h4>
                        </div>
                    </template>

                    <template v-if="cambiar_pagina == 0">
                        <div class="p-grid p-fluid">
                            <div class="p-col-12 p-md-4">
                                <div class="p-inputgroup">
                                    <span class="p-inputgroup-addon">
                                        <i class="pi pi-folder"></i>
                                    </span>
                                    <InputText class="p-inputtext-sm" placeholder="Documento" :readonly="casosEspeciales" v-model="documento" ref="documentoRef" @keyup.enter="fetchClienteData"/>
                                    <span class="p-inputgroup-addon">
                                        <Checkbox v-model="casosEspeciales" :binary="true" @change="habilitarNombreCliente"/>
                                    </span>
                                </div>
                            </div>    

                            <div class="p-col-12 p-md-4">
                                <div class="p-inputgroup">
                                <span class="p-inputgroup-addon">
                                    <i class="pi pi-user"></i>
                                </span>
                                    <InputText class="p-inputtext-sm" placeholder="Nombre del cliente" v-model="cliente" ref="nombreRef"/>
                                </div>
                            </div>

                            <div class="p-col-12 p-md-4">
                                <div class="p-inputgroup">
                                    <span class="p-inputgroup-addon">
                                        <i class="pi pi-google"></i>
                                    </span>
                                    <InputText class="p-inputtext-sm" placeholder="Correo electronico" v-model="email" ref="email"/>
                                </div>
                            </div>
                        </div>

                        <div class="p-grid p-fluid align-items-stretch">
                            <!--<div class="p-col-12 p-md-4 p-mt-4">
                                <span class="p-float-label">
                                <InputText id="mesero" type="text" v-model="usuario_autenticado" ref="mesero" class="p-inputtext-sm" readonly />
                                <label for="mesero">Usuario</label>
                                </span>
                            </div>-->
                            <div class="p-col-12 p-md-4 p-mt-4">
                                <span class="p-float-label">
                                <Dropdown
                                    class="p-inputtext-sm"
                                    id="comprobante"
                                    v-model="tipo_comprobante"
                                    :options="lista_comprobantes"
                                    optionLabel="name"
                                    optionValue="code"
                                    placeholder="Seleccione"
                                />
                                <label for="comprobante">Tipo Comprobante</label>
                                </span>
                            </div>

                            <div class="p-col-12 p-md-4 p-mt-4">
                                <span class="p-float-label">
                                <InputText id="numero-ticket" type="text" v-model="num_comprob" class="p-inputtext-sm" ref="numeroComprobanteRef" readonly />
                                <label for="numero-ticket">Numero Comanda</label>
                                </span>
                            </div>
                       
                            
                        </div>
                        <div class="p-grid p-fluid align-items-stretch">
                            <!--<div class="p-col-12 p-md-4 p-mt-4">
                                <span class="p-float-label">
                                <InputText id="mesero" type="text" v-model="usuario_autenticado" ref="mesero" class="p-inputtext-sm" readonly />
                                <label for="mesero">Usuario</label>
                                </span>
                            </div>-->
                            <!--<div class="p-col-12 p-md-4 p-mt-4">
                                <span class="p-float-label">
                                <Dropdown
                                    class="p-inputtext-sm"
                                    id="comprobante"
                                    v-model="tipo_comprobante"
                                    :options="lista_comprobantes"
                                    optionLabel="name"
                                    optionValue="code"
                                    placeholder="Seleccione"
                                />
                                <label for="comprobante">Tipo Comprobante</label>
                                </span>
                            </div>-->

                            <!--<div class="p-col-12 p-md-4 p-mt-4">
                                <span class="p-float-label">
                                <InputText id="numero-ticket" type="text" v-model="num_comprob" class="p-inputtext-sm" ref="numeroComprobanteRef" readonly />
                                <label for="numero-ticket">Numero Comanda</label>
                                </span>
                            </div>-->

                            <div class="p-col-6 p-md-6 buttons-container">
                                <SelectButton v-model="tipo_entrega" :options="justifyOptions" optionLabel="label" optionValue="value" class="custom-select-button">
                                    <template #option="slotProps">
                                        <div class="custom-button-content">
                                            <i :class="slotProps.option.icon"></i>
                                            <span>{{ slotProps.option.label }}</span>
                                        </div>
                                    </template>
                                </SelectButton>
                            </div>

                            <div class="p-col-6 p-md-6" v-if="['Afuera', 'Planta Alta', 'Planta Baja', 'Patio'].includes(tipo_entrega)">
                                <span class="p-float-label">
                                    <Dropdown
                                        class="p-inputtext-sm"
                                        id="mesa"
                                        v-model="mesaSeleccionada"
                                        :options="arrayMesas"
                                        optionLabel="nombre"
                                        optionValue="id"
                                        placeholder="Seleccione Mesa"
                                        @change="onMesaChange"
                                    />
                                </span>
                            </div>

                            <!--<div class="p-col-12 p-md-4 p-mt-4">
                                <span class="p-float-label">
                                    <InputText id="numero-factura" type="text" v-model="num_factura" class="p-inputtext-sm" ref="numeroFacturaRef" readonly />
                                    <label for="numero-factura">Numero Factura</label>
                                </span>
                            </div>-->
                        </div>

                    <DataTable
                        :value="arrayDetalle"
                        responsiveLayout="scroll"
                    >
                        <Column field="articulo" header="Articulo">
                            <template #body="slotProps">
                                {{ (slotProps.data.articulo).toUpperCase() }}
                            </template>
                        </Column>

                        <Column field="cantidad" header="Cantidad">
                            <template #body="slotProps">
                                <InputNumber
                                    readonly
                                    class="p-inputtext-sm"
                                    v-model="slotProps.data.cantidad"
                                    showButtons
                                    buttonLayout="horizontal"
                                    :step="1"
                                    :min="1"
                                    :max="slotProps.data.stock"
                                    decrementButtonClass="p-button-danger"
                                    incrementButtonClass="p-button-success"
                                    incrementButtonIcon="pi pi-plus"
                                    decrementButtonIcon="pi pi-minus"
                                    @input="actualizarArrayProductos(slotProps.index)"
                                />
                            </template>
                        </Column>


                        <Column field="observacion" header="Observaciones">
                            <template #body="slotProps">
                                <Textarea v-model="slotProps.data.observacion" class="p-inputtext-sm" :autoResize="true" rows="1" cols="20" />
                            </template>
                        </Column>

                        <Column field="subtotal" header="Subtotal">
                            <template #body="slotProps">
                                {{ (slotProps.data.precio * slotProps.data.cantidad - slotProps.data.descuento).toFixed(2) }}
                            </template>
                        </Column>

                        <Column header="Opciones">
                            <template #body="slotProps">
                            <Button
                                icon="pi pi-trash"
                                class="p-button-rounded p-button-danger"
                                @click="eliminarDetalle(slotProps.index)"
                            />
                            </template>
                        </Column>
                        
                        <!--<template #footer>
                        <tr style="background-color: #CEECF5;">
                            <td colspan="3" align="right"><strong>Sub Total: Bs.</strong></td>
                            <td>{{ totalParcial= (calcularSubTotal).toFixed(2) }}</td>
                        </tr>
                        <tr style="background-color: #CEECF5;">
                            <td colspan="3" align="right"><strong>Total Neto: Bs.</strong></td>
                            <td>{{ total= (calcularTotal).toFixed(2) }}</td>
                        </tr>
                        </template>-->
                        <template #empty>
                            <h6>Carrito vacio</h6>
                        </template>

                        <template #loading>
                            <h6>Cargando el carrito ...</h6>
                        </template>
                    </DataTable>

                    <Card class="summary-card">
                        <template #content>
                        <!--<div class="p-grid p-align-center">
                            <div class="p-col-6 p-text-right"><strong>Sub Total: Bs.</strong></div>
                            <div class="p-col-6">{{ totalParcial= (calcularSubTotal).toFixed(2)}}</div>
                        </div>-->
                        <div class="p-grid p-align-center">
                            <div class="p-col-6 p-text-right"><strong>Total: Bs.</strong></div>
                            <div class="p-col-6">{{ (calcularTotal).toFixed(2)}}</div>
                        </div>
                        </template>
                    </Card>

                    
                    
                    </template>

                    <template v-if="cambiar_pagina == 1">
                        <TabView>
                                <TabPanel header="Efectivo">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-7">
                                                <div class="card shadow-sm">
                                                    <div class="card-body">

                                                        <form>
                                                            <div class="form-group">
                                                                <label for="montoEfectivo"><i class="fa fa-money mr-2"></i>
                                                                    Monto
                                                                    Recibido:</label>
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">
                                                                            {{
                                                                            monedaVenta[1] }}
                                                                        </span>
                                                                    </div>
                                                                    <input type="number" class="form-control"
                                                                        id="montoEfectivo" v-model="recibido"
                                                                        placeholder="Ingrese el monto recibido">
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <label for="cambioRecibir"><i
                                                                        class="fa fa-exchange mr-2"></i>
                                                                    Cambio a Entregar:</label>
                                                                <input type="text" class="form-control" id="cambioRecibir"
                                                                    placeholder="Se calculará automáticamente"
                                                                    :value="recibido - calcularTotal.toFixed(2)"
                                                                    readonly>
                                                            </div>


                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="card shadow-sm">
                                                    <div class="card-body">
                                                        <div class=" mb-3">
                                                            <h5 class="mb-0"> Detalle
                                                                de Venta
                                                            </h5>

                                                        </div>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span><i class="fa fa-dollar mr-2"></i> Monto Total:</span>
                                                            <span class="font-weight-bold">
                                                                {{ total=(calcularTotal).toFixed(2) }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span><i class="fa fa-tag mr-2 text-success"></i>
                                                                Descuento:</span>
                                                            <span class="font-weight-bold text-success">0.00</span>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="codigoDescuento"><i class="fa fa-gift mr-2"></i>
                                                                Código de Descuento Gift Card:</label>
                                                            <div class="input-group mb-3">
                                                                <input type="number" class="form-control" id="descuentoGiftCard" v-model="descuentoGiftCard" min="0">    
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="numeroTarjeta"><i class="fa fa-credit-card mr-2"></i> Número de Tarjeta:</label>
                                                            <div class="input-group mb-3">
                                                                <input type="text" class="form-control" id="numeroTarjeta" v-model="numeroTarjeta" placeholder="Ingrese el número de tarjeta">
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="d-flex justify-content-between">
                                                            <span><i class="fa fa-money mr-2"></i> Total a Pagar:</span>
                                                            <span class="font-weight-bold h5">
                                                                {{ total=(calcularTotal).toFixed(2) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" @click="aplicarDescuento" 
                                                class="btn btn-success btn-block"><i class="fa fa-check mr-2"></i> 
                                                Registrar Pago</button>
                                            </div>
                                        </div>
                                    </div>
                                </TabPanel>

                                <!--<TabPanel header="Gift Card">
                                    <div>
                                        <div class="mt-4">
                                            <form>
                                                <div class="form-group">
                                                    <label for="descuentoGiftCard"><i class="fa fa-tag mr-2"></i> Monto de la Gift Card:</label>
                                                    <div class="input-group mb-3">
                                                        <input type="number" class="form-control" id="descuentoGiftCard" v-model="descuentoGiftCard" min="0">
                                                    </div>
                                                </div>
                                                <button type="button" @click="registrarVenta(27)" class="btn btn-success btn-block"><i class="fa fa-check mr-2"></i> Confirmar</button>
                                            </form>
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
                                                <button type="button" @click="aplicarCombinacion" class="btn btn-success btn-block"><i class="fa fa-check mr-2"></i> Confirmar</button>
                                            </form>
                                        </div>
                                    </div>
                                </TabPanel>-->

                                <TabPanel header="QR">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div>
                                            <InputText v-model="alias" readonly style="display: none;" />
                                            <br>
                                            <label for="montoQR">Monto:</label>
                                            <span class="font-weight-bold">{{ total = calcularTotal.toFixed(2) }}</span>
                                            <br>      
                                            <Button @click="generarQr" label="Generar QR" />
                                            
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

                                            <!-- Botón para registrar la venta 
                                            <button
                                                v-if="((idrol === 1 && tipo_entrega === 'Entregas')   || (idrol === 2 && estadoTransaccion && estadoTransaccion.objeto.estadoActual === 'PAGADO' ) || (idrol === 1 && tipo_entrega != 'Entregas' && estadoTransaccion && estadoTransaccion.objeto.estadoActual === 'PAGADO'  ))" 
                                                type="button" @click="registrarVenta(7)" class="btn btn-success btn-block">
                                                <i class="fa fa-check mr-2"></i> Confirmar Venta
                                            </button>
                                            -->
                                            <button
                                                v-if="((idrol === 1)   || (idrol === 2 && estadoTransaccion && estadoTransaccion.objeto.estadoActual === 'PAGADO' ))" 
                                                type="button" @click="registrarVenta(7)" class="btn btn-success btn-block">
                                                <i class="fa fa-check mr-2"></i> Confirmar Venta
                                            </button>
                                        </div>
                                    </div>
                                </TabPanel>
                            </TabView>
                    </template>

                    <template #footer>                
                        <div class="footer-buttons">
                            <Button label="Cerrar" icon="pi pi-times" @click="closeDialog" class="p-button-danger" />
                            <Button v-if="cambiar_pagina == 0" label="Siguiente" icon="pi pi-arrow-right" @click="cambiarPaginaVenta" class="p-button-success" />
                        </div>
                    </template>
                </Dialog>

                            <Sidebar :visible.sync="visiblePago" :baseZIndex="1000" position="full">
                            <template #header>
                                <div class="sidebar-full">
                                    <i class="pi pi-money-bill" style="font-size: 2rem"></i>
                                    <h4>Detalle de Pago</h4>
                                </div>
                            </template>

                            </Sidebar>

                    </template>

            </Panel>
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
import Toast from 'primevue/toast';
import ToggleButton from 'primevue/togglebutton';
import SelectButton from 'primevue/selectbutton';
import Checkbox from 'primevue/checkbox';
import Divider from 'primevue/divider';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup';
import Tooltip from 'primevue/tooltip';
import Textarea from 'primevue/textarea';

import { TileSpinner } from 'vue-spinners';
import { readonly } from 'vue';


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
            tipo_entrega: '',
            tipoEntregaOptions: ['Llevar', 'Afuera', 'Patio', 'Planta Baja', 'Planta Alta'],            
            filas_dinamicas: 23,
            arrayCategoriasMenu: [],
            arrayCategoriasProducto: [],
            arrayMenu: [],
            arrayMenuFiltrado: [],
            arrayMesas: [],
            mesaSeleccionada: null,
            layout: 'grid',
            visibleFull: false,
            visiblePago: false,
            
            visibleRight: false,
            cambiar_pagina: 0,
            value: '',
            value2: 0,
            value3: null,
            position: 'right',

            visibleDialog: false,
            dialogStyle: {
                width: '80vw',
            },
            lista_comprobantes: [
                {name: 'RECIBO', code: 'RECIBO'},
                {name: 'FACTURA', code: 'FACTURA'}
                /*{name: 'BOLETA', code: 'BOLETA'}*/
            ],

            tipo_comprobante: 'FACTURA',

            justifyOptions: [
                {icon: 'pi pi-shopping-bag', label: 'Llevar', value: 'Llevar'},
                {icon: 'pi pi-map-marker', label: 'Adentro', value: 'Afuera'},
                {icon: 'pi pi-home', label: 'Patio', value: 'Patio'},
            ],

            categorias_lista: [
                {
                    label: 'Categorias',
                    items: [{
                        label: 'Comidas',
                        icon: 'pi pi-bookmark-fill',
                        command: () => {
                            this.updateProducts('Comidas')
                        }
                    },
                    {
                        label: 'Bebidas',
                        icon: 'pi pi-bookmark-fill',
                        command: () => {
                            this.updateProducts('Bebidas')
                        }
                    }
                ]}
            ],

            lista_subcategorias: [],
            subcategoria_seleccionada: 'todos',

            almacenes_lista: [
                {
                    label: 'Almacenes',
                    items: []
                }
            ],
            id_sucursal_actual: '1',
            categoria_general: 'comidas',
            arraySucursal: [],
            arrayAlmacen: [],

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

            // ------ Tarifa delivery
            tarifa_delivery: 0,
            tarifaDeliveryIncluida: false, // Nueva propiedad para el checkbox

           

            // -----------------------

            venta_id: 0,
            idcliente: null,
            usuarioAutenticado: null,
            puntoVentaAutenticado: null,
            cliente: '',
            email: 'trotamundos.rockpopbar.cba@gmail.com',
            mesa: '',
            nombreCliente: '',
            documento: '',
            tipo_documento: '',
            complemento_id: '',
            descuentoAdicional: 0.00,
            descuentoGiftCard: '',
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
            actividadEconomica: '',
            precio: 0,
            cantidad: 1,
            descuento: 0,
            descuentoProducto: 0,
            sTotal: 0,
            stock: 0,
            valorMaximoDescuento: '',
            mostrarElementos: true,
            mostrarDireccion: false,
            mostrarCUFD: false,
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
            menu: 0 ,
            tipoPago: 'EFECTIVO',
            tiposPago: {
                        EFECTIVO: 1,
                        TARJETA: 2,
                        QR: 3
                        },
        }
    },
    directives: {
        'tooltip': Tooltip
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
        Toast,
        ToggleButton,
        SelectButton,
        Checkbox,
        Divider,
        DataTable,
        Column,
        ColumnGroup,
        Textarea
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

        tarifaDeliveryIncluida(newVal) {
            console.log('Estado del checkbox de Tarifa Delivery:', newVal);
        },

        tipo_comprobante(newVal) {
            console.log('Nuevo valor del tipo_comprobante:', newVal);
        },
        

        /*tipo_entrega(newVal) {
            console.log('Tipo de entrega seleccionado:', newVal);
        },*/


        subcategoria_seleccionada(newVal) {
            this.filtrarMenuPorSubcategoria(newVal)
        }
    },

    computed: {

        isMobile() {
            return window.innerWidth <= 768; // Ajusta el ancho según tus necesidades
        },

        /*mostrarMesa() {
            return this.tipo_entrega === 'Aqui';
        },*/

        mostrarDelivery() {
            this.tarifa_delivery = 0;
            this.tarifaDeliveryIncluida = false,
            this.telefono_delivery = '',
            this.direccion_delivery = ''

            return this.tipo_entrega === 'Entregas';
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

        /*calcularTotal: function () {
            var resultado = 0.0;
            for (var i = 0; i < this.arrayDetalle.length; i++) {
                resultado += (this.arrayDetalle[i].precio * this.arrayDetalle[i].cantidad - this.arrayDetalle[i].descuento);
            }
            resultado -= this.descuentoAdicional;

            if (this.tarifaDeliveryIncluida) {
                resultado += this.tarifa_delivery;
            }

            return resultado;
        },*/

        calcularTotal: function () {
            var resultado = 0.0;
            if (this.tarifaDeliveryIncluida){
                for (var i = 0; i < this.arrayDetalle.length; i++) {
                resultado += (this.arrayDetalle[i].precio * this.arrayDetalle[i].cantidad - this.arrayDetalle[i].descuento);
                }
                resultado += this.tarifa_delivery;
            }else{
                for (var i = 0; i < this.arrayDetalle.length; i++) {
                resultado += (this.arrayDetalle[i].precio * this.arrayDetalle[i].cantidad - this.arrayDetalle[i].descuento);
                }
                resultado -= this.descuentoAdicional;
            }

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
        },

        listaMesas() {
            return this.arrayMesas.map(mesa => ({
                label: mesa.nombre,
                value: mesa.id
            }));
        }
    
    },

    methods: {
        filtrarSucursalPorId(idSucursal) {
            let listaSucursal = this.arraySucursal.filter(sucursal => sucursal.id === idSucursal);
            return listaSucursal[0].nombre;
        },

        filtrarMenuPorSubcategoria(objetoCategoria) {
            if (objetoCategoria.value == 'todos') {
                this.arrayMenuFiltrado = this.arrayMenu;
            } else {
                this.arrayMenuFiltrado = this.arrayMenu.filter(producto => producto.idcategoria_articulo === objetoCategoria.value);
            }
        },



        mostrarInformacionProducto(data) {
            let me = this;
            let stock = (data.saldo_stock === null)? 0 : (data.saldo_stock === undefined)? 'Sin limite': data.saldo_stock;
            let almacen = (me.categoria_general === 'comidas')? 'Sin almacen': data.nombre_almacen;
            let sucursal = (me.categoria_general === 'comidas')? 'Todas': data.nombre_sucursal
            let mensaje = `Stock: ${stock}
                Almacen: ${almacen}
                Sucursal: ${sucursal}`

            return mensaje;
        },

        listarAlmacenes() {
            let me = this;
            var url = '/almacen?page=' + 1;
            axios.get(url).then(function (response) {
                let respuesta = response.data;
                me.arrayAlmacen = respuesta.almacenes.data;

                me.almacenes_lista[0].items = me.arrayAlmacen.map(almacen => ({
                    label: almacen.nombre_almacen,
                    icon: 'pi pi-tag',
                    command: () => {
                        me.filtrarProductosPorAlmacen(almacen.id);
                    }
                    }));
                })
            .catch(function (error) {
                console.log(error);
            });
        },

        selectSucursal() {
            let me = this;
            var url = '/sucursal/selectSucursal';
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                me.arraySucursal = respuesta.sucursales;

                /*me.sucursales_lista[0].items = me.arraySucursal.map(sucursal => ({
                label: sucursal.nombre,
                icon: 'pi pi-tag',
                command: () => {
                    //me.listarMenu(sucursal.id);
                    //me.listarProducto(this.buscar, this.criterio, this.id_sucursal_actual);
                    me.filtrarProductosPorSucursal(sucursal.id);
                    esto para aumentar
                }
                }));*/
            })
            .catch(function (error) {
                console.log(error);
            });
        },

        filtrarProductosPorSucursal(idSucursal) {
            if (this.categoria_general === 'bebidas') {
                this.listarProducto(this.buscar, this.criterio, idSucursal);
            }
        },

        filtrarProductosPorAlmacen(idAlmacen) {
            if (this.categoria_general === 'bebidas') {
                this.listarProducto(this.buscar, this.criterio, idAlmacen);
            }
        },

        getCardClass(product) {
            if (this.categoria_general === 'bebidas') {
                if (product.saldo_stock === 0 || product.saldo_stock === null) {
                    return 'stock-cero';
                } else if (product.saldo_stock <= product.stockmin) {
                    return 'stock-minimo';
                }
            }

            return 'project-card';
        },

        handleProductClick(product) {
            if (product.saldo_stock > 0) {
                this.agregarDetalleModal(product);
            }
        },

        cambiarPaginaVenta() {
            if (this.cliente == ''){
                this.$toast.add({
                    severity:'error',
                    summary: 'Sin nombre de Cliente',
                    detail: `Nombre del Cliente necesario`,
                    life: 2000});

                return;
            }

            if (this.tipo_entrega == '') {
                this.$toast.add({
                    severity:'error',
                    summary: 'Sin Tipo de Entrega',
                    detail: `Seleccione un Tipo de Entrega`,
                    life: 2000});

                return;
            }

            if (this.documento == ''){
                this.$toast.add({
                    severity:'error',
                    summary: 'Sin documento del Cliente',
                    detail: `Documento del Cliente necesario`,
                    life: 2000});

                return;
            }
            if (this.tipo_entrega == 'Afuera' || this.tipo_entrega == 'Patio'){
                if (this.mesaSeleccionada == null) {
                    this.$toast.add({
                        severity:'error',
                        summary: 'Sin Numero de mesa',
                        detail: `Ingrese el número de mesa`,
                        life: 2000});

                    return;
                }
            }

            if (this.tipo_entrega == 'Entregas') {
                if (this.telefono_delivery == '') {
                    this.$toast.add({
                        severity:'error',
                        summary: 'Sin Telefono Delivery',
                        detail: `Ingrese el Telefono del Delivery`,
                        life: 2000});

                    return;
                }

                else if (this.direccion_delivery == '') {
                    this.$toast.add({
                    severity:'error',
                    summary: 'Sin Direccion de Entrega',
                    detail: `Ingrese la direccion de Entrega Delivery`,
                    life: 2000});

                    return;
                }

                else if (this.deliverySeleccionado == '') {
                    this.$toast.add({
                        severity:'error',
                        summary: 'Sin Repartidor',
                        detail: `Seleccione un Repartidor para entregar`,
                        life: 2000});

                    return;
                }

                else if (this.tarifa_delivery == '') {
                    this.$toast.add({
                        severity:'error',
                        summary: 'Sin Tarifa de Delivery',
                        detail: `Ingrese una tarifa parqa el Delivery`,
                        life: 2000});

                    return;
                }

                
            }
            this.cambiar_pagina = 1;
        },

        onMesaChange() {
            console.log("Mesa seleccionada:", this.mesaSeleccionada);
        },


        updateDialogStyle() {
            if (window.innerWidth <= 576) {
                this.dialogStyle = {
                    width: 'calc(100% - 10px)',
                    margin: '5px'
                };
            } else {
                this.dialogStyle = {
                    width: '70vw'
                };
            }
        },

        toggleCart() {
            if (this.isMobile) {
                this.position = 'full';
                
            } else {
                this.position = 'right';
                
            }
            this.visibleDialog = true;
        },

        closeDialog() {
            this.visibleDialog = false;
            this.cambiar_pagina = 0;
        },

        actualizarFechaHora() {
            const now = new Date();
            this.alias = now.toLocaleString();
        },

        toggle_menu(event) {
            this.$refs.menu.toggle(event);
        },

        toggle_almacen(event) {
            this.$refs.almacen.toggle(event);
        },

        updateProducts(categoria) {
            if (categoria === 'Comidas') {
                this.listarMenu();
                this.categoria_general = 'comidas';
            } else if (categoria === 'Bebidas') {
                this.listarProducto(this.buscar, this.criterio, this.id_sucursal_actual);
                this.categoria_general = 'bebidas';
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

        generarQr() {
            this.aliasverificacion = this.alias;
            console.log('Generar QR con alias:', this.aliasverificacion);

            axios.post('/qr/generarqr', {
                alias: this.alias,
                monto: this.calcularTotal
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

    truncateAndCapitalize(text) {
        const maxLength = 13;
        text = text.length > maxLength ? text.substring(0, maxLength - 3) + '...' : text;
        return text.replace(/\b\w/g, (char) => char.toUpperCase());
    },

    editarMenu(event) {
        event.stopPropagation();
        console.log('EDITANDO MENU');
    },

        actualizarLimiteTabla() {
            const windowWidth = window.innerWidth;

            if (windowWidth <= 576) {
                this.filas_dinamicas = 6;
            } else {
                this.filas_dinamicas = 23;
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

                me.lista_subcategorias = me.arrayCategoriasMenu.map(subcategoria => ({
                    label: subcategoria.nombre,
                    icon: 'pi pi-tag',
                    value: subcategoria.id,
                }));
                me.lista_subcategorias.unshift({icon: 'pi pi-tag', label: 'Todos', value: 'todos'})

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

                me.lista_subcategorias = me.arrayCategoriasProducto.map(subcategoria => ({
                    label: subcategoria.nombre,
                    icon: 'pi pi-tag',
                    value: subcategoria.id,
                }));
                me.lista_subcategorias.unshift({icon: 'pi pi-tag', label: 'Todos', value: 'todos'})
                console.log('categorias bebidas: ', me.lista_subcategorias)
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

        cargarSucursales() {
            let me = this;
                axios.get('/sucursal/selectSucursal')
                    .then(function (response) {
                        var respuesta = response.data;
                        me.listaSucursales = respuesta.sucursales;
                    })
                    .catch(function (error) {
                        console.error('Error al cargar las sucursales:', error);
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
                        swal(
                            'CLIENTE NO ENCONTRADO',
                            'Por favor, verifique los datos ingresados',
                            'error'
                        )
                        this.cliente = '';
                        this.email = 'trotamundos.rockpopbar.cba@gmail.com';
                    }
                } catch (error) {
                    console.error('Error al buscar los datos del cliente:', error);
                }
            }
        },

        habilitarNombreCliente() {
            console.log('habilitar nombre')
            if (this.casosEspeciales) {
                //this.$refs.documentoRef.setAttribute("readonly", true);
                this.documento = "99001";
                //this.idcliente = "2";
                this.tipo_documento = "5"; 
            } else {
                //this.$refs.documentoRef.removeAttribute("readonly");
                this.documento = "";
                //this.idcliente = "";
                this.tipo_documento = "";
            }
        },

        async verificarComunicacion() {
        try {
        const response = await axios.post('/venta/verificarComunicacion');
        if (response.data.RespuestaComunicacion.transaccion === true) {
            document.getElementById("comunicacionSiat").innerHTML = response.data.RespuestaComunicacion.mensajesList.descripcion;
            document.getElementById("comunicacionSiat").className = "badge bg-success";
        } else {
            document.getElementById("comunicacionSiat").innerHTML = "Desconectado";
            document.getElementById("comunicacionSiat").className = "badge bg-secondary";
        }
        } catch (error) {
        console.log(error);
        }
    },

    async cuis() {
        try {
        const response = await axios.post('/venta/cuis');
        if (response.data.RespuestaCuis.transaccion === false) {
            document.getElementById("cuis").innerHTML = "CUIS: " + response.data.RespuestaCuis.codigo;
            document.getElementById("cuis").className = "badge bg-primary";
        } else {
            document.getElementById("cuis").innerHTML = "CUIS: Inexistente";
            document.getElementById("cuis").className = "badge bg-secondary";
        }
        } catch (error) {
        console.log(error);
        }
    },

    async cufd() {
        try {
        const response = await axios.post('/venta/cufd');
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
        } catch (error) {
        console.log(error);
        }
    },

    async ejecutarSecuencial() {
        try {
        await this.verificarComunicacion();
        await this.cuis();
        await this.cufd();
        } catch (error) {
        console.log("Error en la ejecución secuencial:", error);
        }
    },

        async obtenerDatosSesionYComprobante() {
            console.log("El id usuario en comprobante es: " + this.idusuario);
            try {
                const sesionResponse = await axios.get('/obtener-datos-sesion');
                this.scodigorecepcion = sesionResponse.data.scodigorecepcion;
                console.log('Valor de scodigorecepcion:', this.scodigorecepcion);

                const idsucursal = this.idusuario === 1 ? this.sucursalSeleccionada : this.idsucursalusuario;
                const comprobanteResponse = await axios.get('/obtener-ultimo-comprobante', {
                    params: {
                        idsucursal: idsucursal
                    }
                });
                this.num_comprob = comprobanteResponse.data.next_comprobante;
                console.log('Next comprobante:', this.num_comprob);
            } catch (error) {
                console.error('Error al obtener datos de sesión o el último comprobante:', error);
            }
        },


        async onSucursalChange() {
            await this.ejecutarFlujoCompleto();
        },

        async ejecutarFlujoCompleto() {
            await this.obtenerDatosUsuario();
            await this.obtenerDatosSesionYComprobante();
        },

        /*nextNumber() {
            if (!this.num_comprob || this.num_comprob === "") {
                this.last_comprobante++;
                this.num_comprob = this.last_comprobante.toString().padStart(4, "0");
            }
        },*/
        nextNumber() {
            const nextComprobanteNumber = this.last_comprobante + 1;
            console.log('Next comprobante number:', nextComprobanteNumber);
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
            const descuentoGiftCard = this.descuentoGiftCard;
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
                    me.actividadEconomica = me.arrayArticulo[0]['actividadEconomica'];
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

        agregarDetalleModal(data) {

            let me = this;

            if (data.saldo_stock === 0 || data.saldo_stock === null) {
                this.$toast.add({
                    severity:'error',
                    summary: 'Error en stock',
                    detail: `${data.nombre.toUpperCase()} tiene stock ${(data.saldo_stock === null)? 0 : data.saldo_stock}`,
                    life: 1000});
            }
            else {

                let unidadMedida = 57;
                let montoDescuento = 0;
                let numeroSerie = null;
                let numeroImei = null;

                let productoEnCarrito = me.arrayDetalle.find(item => item.codigoComida === data.codigo);
                let productoEnFactura = me.arrayFactura.find(item => item.codigoProducto === data.codigo);

                if (productoEnCarrito) {
                    productoEnCarrito.cantidad += 1;
                    this.$toast.add({
                        severity: 'info',
                        summary: 'Cantidad actualizada',
                        detail: `${data.nombre.toUpperCase()} ha sido incrementado a ${productoEnCarrito.cantidad}`,
                        life: 500
                    });
                } else {
                    me.arrayDetalle.push({
                    codigoComida: data.codigo,
                    articulo: data.nombre,
                    cantidad: 1,
                    precio: data.precio_venta,
                    descuento: 0,
                    stock: data.stock,
                    medida: data.medida,
                    observacion: data.observacion
                    });

                    this.$toast.add({
                        severity: 'success',
                        summary: 'Producto agregado',
                        detail: `${data.nombre.toUpperCase()} ha sido agregado`,
                        life: 1000
                    });

                } if (productoEnFactura){
                    productoEnFactura.cantidad += 1;
                    productoEnFactura.subTotal = productoEnFactura.precioUnitario * productoEnFactura.cantidad;
                    /*this.$toast.add({
                        severity: 'info',
                        summary: 'Cantidad actualizada',
                        detail: `${data.nombre.toUpperCase()} ha sido incrementado a ${productoEnCarrito.cantidad}`,
                        life: 500
                    });*/
                }else{
                    me.arrayFactura.push({
                    actividadEconomica: data.actividadEconomica,
                    codigoProductoSin: data.codigoProductoSin,
                    codigoProducto: data.codigo,
                    descripcion: data.nombre,
                    cantidad: 1,
                    unidadMedida: unidadMedida,
                    precioUnitario: data.precio_venta,
                    montoDescuento: montoDescuento,
                    subTotal: data.precio_venta,
                    numeroSerie: numeroSerie,
                    numeroImei: numeroImei
                    });

                    /*this.$toast.add({
                    severity: 'success',
                    summary: 'Producto agregado',
                    detail: `${data.nombre.toUpperCase()} ha sido agregado`,
                    life: 1000
                    });*/
                }
            }

            console.log("ArrayDetalle:", me.arrayDetalle);
                
        },

        actualizarArrayProductos(index) {
            let detalle = this.arrayDetalle[index];
            let producto = this.arrayFactura[index];

            producto.cantidad = detalle.cantidad;
            producto.subTotal = detalle.cantidad * producto.precioUnitario;

            console.log("Se actualizo el arrayFactura: " + producto);

        },

        listarMenu() {
            let me = this;
            var url = '/menu/getAllMenu';
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                //me.arrayMenu.splice(0, me.arrayMenu.length);
                me.arrayMenu = [];
                //me.arrayMenu = respuesta.articulos;
                me.arrayMenu = respuesta.articulos.map(articulo => {
                    return {
                        ...articulo,
                        observacion: "ninguna"
                    };
                });
                me.arrayMenuFiltrado = [];
                me.arrayMenuFiltrado = me.arrayMenu;
            })
            .catch(function (error) {
                console.log(error);
            });

            this.getCategoriasMenu();
        },

        listarProducto(buscar, criterio, idSucursalActual) {
            let me = this;
            var url = '/articulo?buscar=' +  buscar + '&criterio=' + criterio + '&idSucursalActual=' + idSucursalActual;
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                //me.arrayMenu.splice(0, me.arrayMenu.length);
                me.arrayMenu = [];
                //me.arrayMenu = respuesta.articulos;
                me.arrayMenu = respuesta.articulos.map(articulo => {
                    return {
                        ...articulo,
                        observacion: "ninguna"
                    };
                });

                me.arrayMenuFiltrado = [];
                me.arrayMenuFiltrado = me.arrayMenu;
            })
            .catch(function (error) {
                console.log(error);
            });

            this.getCategoriasProductos();

        },

        async listarMesas() {
            try {
                const response = await axios.get('/mesas');
                this.arrayMesas = response.data;
            } catch (error) {
                console.error("Error al obtener las mesas:", error);
            }
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

            imprimirFactura(id, correo) {
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
            if (!me.documento) me.errorMostrarMsjVenta.push("Ingrese el Documento del Cliente");
            //if (me.tipo_comprobante == 0) me.errorMostrarMsjVenta.push("Seleccione el Comprobante");
            //if (!me.impuesto) me.errorMostrarMsjVenta.push("Ingrese el impuesto de compra");
            if (me.arrayDetalle.length <= 0) me.errorMostrarMsjVenta.push("Ingrese detalles");

            if (me.errorMostrarMsjVenta.length) me.errorVenta = 1;

            return me.errorVenta;
        },

        /*aplicarDescuento() {
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
        },*/

        async registrarVenta(idtipo_pago) {
            let idMesa = this.mesaSeleccionada;

            if (this.validarVenta()) {
                return;
            }

            this.mostrarSpinner = true;
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

                if(this.idrol === 1){
                    this.idsucursalventa = this.sucursalSeleccionada;
                }else{
                    this.idsucursalventa = this.idsucursalusuario;
                }

                const ventaResponse = await axios.post('/venta/registrar', {
                    'idcliente': this.idcliente,
                    'tipo_comprobante': this.tipo_comprobante,
                    'serie_comprobante': this.serie_comprobante,
                    'num_comprobante': this.num_comprob,
                    'impuesto': this.impuesto,
                    'total': this.calcularTotal,
                    'idAlmacen': this.idAlmacen,
                    'idtipo_pago': idtipo_pago,
                    'idtipo_venta': this.idtipo_venta,
                    'idsucursal': this.idsucursalventa,
                    'iddelivery': this.deliverySeleccionado,
                    'primer_precio_cuota': this.primer_precio_cuota,
                    'cliente': this.cliente,
                    'documento': this.documento,
                    'tipoEntrega': idMesa,
                    'observacion': this.tipo_entrega,
                    'numero_cuotasCredito': this.numero_cuotas,
                    'tiempo_dias_cuotaCredito': this.tiempo_diaz,
                    'totalCredito': this.primera_cuota ? this.calcularTotal - this.cuotas[0].totalCancelado : this.calcularTotal,
                    'estadoCredito': "Pendiente",
                    'cuotaspago': this.cuotas,
                    'estado': 1,
                    'data': this.arrayDetalle
                });

                let idVentaRecienRegistrada = ventaResponse.data.id;
                console.log("El ID es: " + idVentaRecienRegistrada);
                this.actualizarFechaHora();

                /*if(tipoEntregaValor === 'M' || tipoEntregaValor === 'L'){
                    this.imprimirTicket(idVentaRecienRegistrada);
                }*/

                if (ventaResponse.data.id > 0) {
                    if(this.tipo_comprobante === "RECIBO"){
                        this.imprimirTicket(idVentaRecienRegistrada);
                        //this.imprimirResivo(idVentaRecienRegistrada);
                        this.visibleDialog = false;
                        this.cambiar_pagina = 0;
                        this.ejecutarFlujoCompleto();
                        this.listarMenu();
                        //this.listarProducto(this.buscar, this.criterio, this.id_sucursal_actual);

                        swal(
                            'VENTA REALIZADA',
                            'Correctamente',
                            'success'
                        )
                        this.categoria_general = 'comidas'
                        this.idtipo_pago = '';
                        this.email = 'trotamundos.rockpopbar.cba@gmail.com';
                        this.recibido = '';
                        this.metodoPago = '';
                        this.cerrarModal2();
                        this.mostrarSpinner = false;
                        this.menu = 49;
                        this.listado = 1;
                        this.idproveedor = 0;
                        this.nombreCliente = '';
                        this.idcliente = '';
                        this.tipo_documento = 0;
                        this.complemento_id = '';
                        this.cliente = '';
                        this.documento = '';
                        this.email = 'trotamundos.rockpopbar.cba@gmail.com';
                        this.imagen = '';
                        this.serie_comprobante = '';
                        this.impuesto = 0.18;
                        this.total = 0.0;
                        this.codigoComida = 0;
                        this.articulo = '';
                        this.cantidad = 0;
                        this.precio = 0;
                        this.stock = 0;
                        this.codigo = '';
                        this.descuento = 0;
                        this.arrayDetalle = [];
                        this.primer_precio_cuota = 0;
                        this.recibido = 0;
                    }else{
                        this.emitirFactura(idVentaRecienRegistrada);
                        this.visibleDialog = false;
                        this.cambiar_pagina = 0;
                        this.ejecutarFlujoCompleto();
                        this.listarMenu();
                        //this.listarProducto(this.buscar, this.criterio, this.id_sucursal_actual);

        
                        this.categoria_general = 'comidas'
                        this.idtipo_pago = '';
                        this.email = 'trotamundos.rockpopbar.cba@gmail.com';
                        this.numeroTarjeta =  null;
                        this.recibido = '';
                        this.metodoPago = '';
                        this.cerrarModal2();
                        this.mostrarSpinner = false;
                        this.menu = 49;
                        this.listado = 1;
                        this.cerrarModal2();
                        this.idproveedor = 0;
                        this.nombreCliente = '';
                        //this.idcliente = null;
                        this.tipo_documento = 0;
                        this.complemento_id = '';
                        this.cliente = '';
                        this.documento = '';
                        this.email = 'trotamundos.rockpopbar.cba@gmail.com';
                        this.imagen = '';
                        this.serie_comprobante = '';
                        this.impuesto = 0.18;
                        this.total = 0.0;
                        this.codigoComida = 0;
                        this.articulo = '';
                        this.cantidad = 0;
                        this.precio = 0;
                        this.stock = 0;
                        this.codigo = '';
                        this.descuento = 0;
                        this.arrayDetalle = [];
                        this.primer_precio_cuota = 0;
                        this.recibido = 0;
                    };
                        return;
                    
                } else {
                    console.log(ventaResponse);
                    if (ventaResponse.data.valorMaximo) {
                        this.visibleDialog = false;
                        this.cambiar_pagina = 0;
                        this.ejecutarFlujoCompleto();
                        swal(
                            'Aviso',
                            'El valor de descuento no puede exceder el ' + ventaResponse.data.valorMaximo,
                            'warning'
                        )
                        return;
                    }if (ventaResponse.data.error) {
                        this.visibleDialog = false;
                        this.cambiar_pagina = 0;
                        this.ejecutarFlujoCompleto();
                        swal(
                            'Aviso',
                            'Error en bebidas ' + ventaResponse.data.valorMaximo,
                            'warning'
                        )
                        return;
        
                    } if (ventaResponse.data.caja_validado) {
                        this.visibleDialog = false;
                        this.cambiar_pagina = 0;
                        this.ejecutarFlujoCompleto();
                        swal(
                            'Aviso',
                            ventaResponse.data.caja_validado,
                            'warning'
                        )
                        return;
                    }
                }
            } catch (error) {
                console.log(error);
            } 
        },

        async enviarVentaPorWhatsApp(venta) {
            try {
                const response = await axios.post('/enviarWhatsappVenta', {
                    venta: venta
                });
                console.log('Mensaje de WhatsApp enviado:', response.data);
            } catch (error) {
                console.error('Error al enviar mensaje de WhatsApp:', error);
            }
        },


        async emitirFactura(idVentaRecienRegistrada) {

        let me = this;

        let idventa = idVentaRecienRegistrada;
        //let numeroFactura = document.getElementById("num_comprobante").value;
        let numeroFacturaPrueba = String(this.num_factura);
        let numeroFactura = numeroFacturaPrueba.padStart(5, '0');
        let cuf = "464646464";
        let cufdValor = document.getElementById("cufdValor");
        console.log("hola aaaa: ", this.cufdValor);
        let numeroTarjeta = this.numeroTarjeta;
        console.log("El numero de tarjeta es: " + numeroTarjeta);
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
        let montoTotal = (this.calcularTotal.toFixed(2));
        //let descuentoAdicional = document.getElementById("descuentoAdicional").value;
        let descuentoAdicional = this.descuentoAdicional;
        //let usuario = document.getElementById("usuarioAutenticado").value;
        let usuario = this.usuarioAutenticado;
        //let codigoPuntoVenta = document.getElementById("puntoVentaAutenticado").value;
        let codigoPuntoVenta = this.puntoVentaAutenticado;
        let montoGiftCard = document.getElementById("descuentoGiftCard").value;
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
            id_cliente: this.idcliente,
            idventa: idventa,
            correo: correo,
            cufd: cufd
        })
            .then(function (response) {
                var data = response.data;
                var mensaje = data.mensaje;
                var idFactura = data.idFactura;
                console.log('Mensaje:', mensaje);
                console.log('ID de la factura:', idFactura);

                if (mensaje === "VALIDADA") {
                    me.visibleDialog = false;
                    me.cambiar_pagina = 0;
                    me.ejecutarFlujoCompleto();
                    me.obtenerNumeroFactura();
                    
                    swal(
                        'FACTURA VALIDADA',
                        'Correctamente',
                        'success'
                    )
                    me.imprimirTicket(idVentaRecienRegistrada);
                    me.imprimirFactura(idFactura, correo);
                    
                    me.arrayFactura = [];
                    me.codigoExcepcion = 0;
                    me.idtipo_pago = '';
                    me.email = 'trotamundos.rockpopbar.cba@gmail.com';
                    me.descuentoGiftCard = '';
                    me.numeroTarjeta =  null;
                    me.recibido = '';
                    me.metodoPago = '';
                    me.idcliente = null;
                    me.cerrarModal2();
                    me.mostrarSpinner = false;
                    me.menu = 49;
                    me.tipo_comprobante = 'FACTURA';

                } else{
                    me.visibleDialog = false;
                    me.cambiar_pagina = 0;
                    me.arrayFactura = [];
                    me.codigoExcepcion = 0;
                    me.idtipo_pago = '';
                    me.descuentoGiftCard = '';
                    me.numeroTarjeta =  null;
                    me.recibido = '';
                    me.idcliente = null;
                    me.metodoPago = '';
                    me.last_comprobante = '';
                    me.cerrarModal2();
                    me.mostrarSpinner = false;
                    me.tipo_comprobante = 'FACTURA';

                    swal(
                        'FACTURA RECHAZADA',
                        data,
                        'warning'
                    );
                    me.eliminarVenta(idVentaRecienRegistrada);
                }
            })
            .catch(function (error) {
                console.error("Este es el error: " + error);
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
                me.idcliente = null;
                me.metodoPago = '';
                me.eliminarVentaFalloSiat(idVentaRecienRegistrada);
                me.ejecutarFlujoCompleto();

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

        eliminarVentaFalloSiat(idVenta) {
            axios.delete('/venta/eliminarVentaFalloSiat/' + idVenta)
                .then(function (response) {
                    console.log('Venta eliminada correctamente:', response);
                })
                .catch(function (error) {
                    console.error('Error al eliminar la venta:', error);
                });
        },

    imprimirTicket(id) {
        axios.get('/venta/imprimir/' + id)
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

    imprimirFactura(id, correo) {
    console.log("El boton ROLLO fue presionado");
    axios.get('/factura/imprimirRollo/' + id + '/' + correo, { responseType: 'json' })
        .then(function (response) {
            const url = response.data.url;
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'facturaRollo.pdf');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            console.log("Se generó la factura en Rollo correctamente");
        })
        .catch(function (error) {
            console.log(error);
        });
},

    imprimirResivo(id) {
        console.log("Se seleccionó imprimir en ROLLO");
        axios.get("/resivo/imprimirRollo/" + id, {responseType: "blob",})
            .then(function (response) {
                const url = window.URL.createObjectURL(
                new Blob([response.data])
                );
            const link = document.createElement("a");
            link.href = url;
            link.setAttribute("download", "recibo_rollo.pdf");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
                console.log("Se imprimió el recibo en ROLLO correctamente");
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
                    me.tipo_comprobante = 'FACTURA';
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
            this.modal = 1;
            this.tituloModal = 'Seleccione los articulos que desee';

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
        this.updateDialogStyle();
        window.addEventListener('resize', this.updateDialogStyle);

        window.addEventListener('keydown', this.atajoButton);
        //this.obtenerDatosUsuario();
        this.listarMenu();

        //this.listarProducto(1, this.buscar, this.criterio);
        //this.getCategoriasMenu();
        //this.getCategoriasProductos();

        this.ejecutarSecuencial();
        this.actualizarLimiteTabla();
        window.addEventListener('resize', this.actualizarLimiteTabla);

        this.actualizarFechaHora();
        this.cargarSucursales();
        this.selectSucursal();
        this.listarAlmacenes();
        this.ejecutarFlujoCompleto();
        //this.listarProducto(this.buscar, this.criterio, this.id_sucursal_actual);
        this.obtenerNumeroFactura();
        this.listarMesas();
    },

    beforeDestroy() {
        window.removeEventListener('resize', this.updateDialogStyle);
        window.removeEventListener('resize', this.actualizarLimiteTabla);
    }
}
</script>

<style>

.product-container {
    transition: transform 0.2s ease;
}

.product-container:active {
    transform: scale(0.75);
}

/* Sidebar */
.p-panel .p-panel-header {
    padding: 10px;
}

.p-panel .p-panel-content {
    padding: 10px 3px;
}

/* Botones -> Card */
.project-card {
    border: 2px solid #ccc;
    border-radius: 25px;
    padding: 0px;
    margin-bottom: 0px;
    cursor: pointer;

    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border-color: #0bda50;

    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease, background-color 0.3s ease;
}

.project-card:hover {
    transform: scale(1.09); /* Agrandar ligeramente */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Añadir sombra */
    border-color: #3b82f6; /* Cambiar color del borde */
    background-color: #f0f8ff; /* Cambiar color de fondo */
}

.stock-minimo {
    border: 2px solid #ccc;
    border-radius: 25px;
    padding: 0px;
    margin-bottom: 0px;
    cursor: pointer;

    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border-color: #f1952b;

    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease, background-color 0.3s ease;
}

.stock-minimo:hover {
    transform: scale(1.09); /* Agrandar ligeramente */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Añadir sombra */
    background-color: #f0f8ff;
}

.stock-cero {
    border: 2px solid #ccc;
    border-radius: 25px;
    padding: 0px;
    margin-bottom: 0px;
    cursor: pointer;
    opacity: 0.6;

    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border-color: #f12b2b;

    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease, background-color 0.3s ease;
}

.stock-cero:hover {
    transform: scale(1.09); /* Agrandar ligeramente */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Añadir sombra */
    background-color: #f0f8ff;
}
/* ----------------------------------- */

.p-button {
    padding: 0px;
}

.p-card .p-card-content {
    padding: 0px;
}

.p-card .p-card-body {
    padding: 2px;
}

.p-card .p-card-footer {
    padding: 0px 1px 1px 1px;
}

.product-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    text-align: center; /* Centra el texto horizontalmente */
    width: 100%;
}

/*.product-button {
    border: 0px solid #ff0000;
    border-radius: 25px;
    padding: 0;
    margin: 0;
}*/

.image-container {
    overflow: hidden;
    border-radius: 25px 25px 0 0;
}

.product-image {
    width: 100%;
    height: auto;
}

.footer-content {
    display: flex;
    justify-content: space-between;
    padding-top: 0;
    margin-left: 0;
    margin-right: 15px;
}

.price {
    margin-top: 10px;
    font-weight: bold;
    font-size: larger;
}

.rounded-bottom-right {
    border-bottom-left-radius: 20px;
}

@media (min-width: 1368px) {
    .product-container {
        width: calc(100% / 8);
    }
}

@media (max-width: 1367px) and (min-width: 992px) {
    .product-container {
        width: calc(100% / 6);
    }
}

@media (max-width: 991px) and (min-width: 577px) {
    .product-container {
        width: calc(100% / 4);
    }
}

@media (max-width: 576px) {
    .product-container {
        width: calc(100% / 2);
    }
}

.floating-buttons {
    position: fixed;
    bottom: 85px;
    right: 20px;
    z-index: 100;
}

.button-badge-container {
    position: relative;
    display: inline-block;
}

.floating-button {
    padding: 25px 35px;
    border: none;
    border-radius: 17px;
    font-size: 28px;
    cursor: pointer;
}

.p-button.p-button-lg {
    padding: 1rem;
}

.floating-badge {
    position: absolute;
    top: -10px;
    right: -10px;
    z-index: 110;
}


/* Sidebar full*/
.sidebar-full {
    padding-right: 20px;
}

.spinner-container {
    position: relative;
}

.spinner-container > * {
    position: absolute; 
    top: 50%; 
    left: 50%;
    transform: translate(-50%, -50%);
}

.spinner-message {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translate(-50%, -170%);
    z-index: 1;
}

.sidebar-icon {
    font-size: 2rem;
    margin-right: 10px; /* Espacio entre el icono y el texto */
}

.sidebar-title {
  font-size: 1.5rem; /* Tamaño de fuente del título */
  margin: 0; /* Eliminar el margen para el h4 */
}

.sidebar-header {
    display: flex;
    align-items: center; /* Centrar verticalmente los elementos */
    padding-right: 20px;
}

/* Menu toggle */
.p-menu {
    padding-left: 5px;
}

.p-menu .p-menuitem-link {
    padding: 0.70rem;
}

.p-menu .p-submenu-header {
    padding: 0.70rem;
}

/* Dialog style */
.p-dialog-mask.p-component-overlay {
    padding-top: 30px;
}

.p-dialog .p-dialog-header {
    padding: 0.75rem 0.8rem 1rem 0.8rem;
}

.p-dialog .p-dialog-content {
    padding: 0.8rem 0.8rem 0.5rem 0.8rem;
}

.p-dialog .p-dialog-footer {
    padding: 0.5rem 2.5rem 1rem 2.5rem;
}

.p-md-4 {
    padding-top: 1rem !important;
    padding-bottom: 0.5rem !important;
    padding-right: 0.5rem !important;
    padding-left: 0.5rem !important;
}

.p-md-6 {
    padding: 2.5rem 0.5rem 0.5rem 0.5rem;
}

/* Botones Tipo Venta */
.buttons-container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100%;
    align-items: center;
}

.custom-select-button {
    width: 100%;
}

.custom-select-button .p-button {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    /*height: 100%;
    width: 100%;*/
    /*height: calc(33.33% - 10px);*/
    margin-top: 0px;
    margin-bottom: 5px;
}

.custom-select-button .custom-button-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 9px 2px 2px 2px;
    border-radius: 5px;
    transition: background-color 0.3s, color 0.3s;
    font-size: 0.5em;
}

.custom-select-button .custom-button-content i {
    font-size: 35px;
    margin-bottom: 5px;
}

.custom-select-button .custom-button-content span {
    font-size: 15px;
}

.custom-select-button .custom-button-content.selected {
    background-color: #007bff;
    color: #ffffff;
}

.fields-container .p-float-label {
    width: 100%;
}

/* DataTable style*/
.summary-card {
    margin-top: 20px;
    padding: 10px;
    background-color: #CEECF5;
    border-radius: 8px;
}

.p-inputnumber-input {
    width: 40px;
}

.p-datatable .p-datatable-tbody > tr > td {
    padding: 0.5rem;
}

/* Footer Dialog */
.footer-buttons {
    display: flex;
    justify-content: space-between;
    width: 100%;
}

/*.p-button-danger {
    background-color: #d32f2f;
    border: none;
}

.p-button-success {
    background-color: #388e3c;
    border: none;
}*/

.p-dialog .p-dialog-footer button{
    padding: 12px;
}

/* Pruebas */
.p-tabview .p-tabview-panels {
    padding: 7px 1px 3px 1px;
}
</style>
