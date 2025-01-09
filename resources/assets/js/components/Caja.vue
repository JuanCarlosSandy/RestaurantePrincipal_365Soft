<template>
    <main class="main">
        <Panel header="Menu Completo" style="font-size: 1.5rem;" :toggleable="false">
                <template #header>
                    <Button label="New" icon="pi pi-plus" class="p-button-sm p-button-success mr-2" @click="abrirDialogCaja" />
                </template>

        <DataTable :value="arrayCaja" :paginator="true" :rows="10" responsiveLayout="scroll"
        >
            <Column field="fechaApertura" header="Fecha Apertura"></Column>
            <Column field="fechaCierre" header="Fecha Cierre"></Column>
            <Column field="saldoInicial" header="Saldo Inicial"></Column>
            <Column field="ventasContado" header="Ventas Efectivo"></Column>
            <Column field="ventasQR" header="Ventas QR"></Column>
            <Column field="ventasTarjeta" header="Ventas Tarjeta"></Column>
            <Column field="depositos" header="Depositos Extras"></Column>
            <Column field="salidas" header="Salidas Extras"></Column>
            <Column field="saldototalventas" header="Saldo Total Ventas"></Column>
            <Column field="saldoCaja" header="Saldo Caja"></Column>
            <Column field="saldoFaltante" header="Saldo Faltante"></Column>
            <Column field="saldoSobrante" header="Saldo Sobrante"></Column>


                <Column field="estado" header="Estado" :sortable="true" :styles="{'min-width':'4rem', 'padding-left':'5px'}">
                    <template #body="slotProps">
                        <Tag v-if="slotProps.data.estado == 1" label="ABIERTO" icon="pi pi-check" class="custom-chip" style="background-color: #22c55e; color: #ffffff;" />
                        <Tag v-else-if="slotProps.data.estado == 0" label="CERRADO" icon="pi pi-times" class="custom-chip" style="background-color: #ef4444; color: #ffffff;"/>
                    </template>
                </Column>

                <Column field="estado" header="Acciones">
                    <template #body="slotProps">
                        <template v-if = "slotProps.data.estado">
                            <template v-if="!mostrarBotonesSecundarios">
                                <Button type="button" @click="abrirDialogDeposito(slotProps.data.id)" class="btn btn-primary btn-sm">
                                    <i class="icon-plus"></i>
                                </Button> &nbsp;

                                <Button type="button" @click="abrirDialogSalida(slotProps.data.id)" class="btn btn-danger btn-sm">
                                    <i class="icon-minus"></i>
                                </Button> &nbsp;

                                <Button type="button" @click="abrirDialogVer(slotProps.data.id)" class="btn btn-warning btn-sm">
                                    <i class="icon-eye"></i>
                                </Button> &nbsp;

                                <Button type="button" @click="abrirDialogArqueo(slotProps.data.id, slotProps.data.saldoCaja)" class="btn btn-success btn-sm">
                                    <i class="icon-calculator"></i>
                                </Button> &nbsp;
                                            
                            </template>

                            <template v-else>
                                <Button type="button" @click="abrirModal4('cajaVer', 'ver', slotProps.data.id)" class="btn btn-warning btn-sm">
                                    <i class="icon-eye"></i>
                                </Button> &nbsp;

                                <Button type="button" class="btn btn-danger btn-sm" @click="cerrarCaja(slotProps.data.id)">
                                    <i class="icon-lock"></i>
                                </Button>
                            </template>
                        </template>
                                    
                        <template v-else>
                            <Button type="button" @click="datosreportecaja(slotProps.data.id)" class="btn btn-danger btn-sm">
                                <i class="icon-printer"></i>
                            </Button>
                        </template>
                    </template>
                </Column>
        </DataTable>

    <Dialog
        :visible.sync="nuevoCajaDialog"
        :modal="true"
        :closable="false"
        class="contenedor-dialog"
        position="center"
        >
        
        <template #header>
            <div class="p-grid p-align-center">
                <div class="p-col">
                    <i class="pi pi-file sidebar-icon"></i>
                    <h4 class="sidebar-title">ARQUEO DE CAJA</h4>
                </div>
                <div class="p-col" style="text-align: right;">
                    <strong>Total Efectivo: {{ totalEfectivoAbrirCaja }}</strong>
                </div>
            </div>
        </template>

        <div class="p-grid p-fluid">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="p-grid">
                    <div class="p-col-12 p-md-6">
                        <div class="p-grid p-align-center">
                            <div class="p-col">
                                <h4>BILLETES</h4>
                            </div>
                            <div class="p-col" style="text-align: right;">
                                <span class="font-weight-bold">Total = {{ totalBilletes }}</span>
                            </div>
                        </div>

                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 200:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="billete200" placeholder="0" />
                            </div>
                        </div>
                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 100:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="billete100" placeholder="0" />
                            </div>
                        </div>
                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 50:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="billete50" placeholder="0" />
                            </div>
                        </div>
                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 20:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="billete20" placeholder="0" />
                            </div>
                        </div>
                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 10:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="billete10" placeholder="0" />
                            </div>
                        </div>
                    </div>
                    <div class="p-col-12 p-md-6">
                        <div class="p-grid p-align-center">
                            <div class="p-col">
                                <h4>MONEDAS</h4>
                            </div>
                            <div class="p-col" style="text-align: right;">
                                <span class="font-weight-bold">Total = {{ totalMonedas }}</span>
                            </div>
                        </div>

                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 5:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="moneda5" placeholder="0" />
                            </div>
                        </div>
                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 2:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="moneda2" placeholder="0" />
                            </div>
                        </div>
                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 1:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="moneda1" placeholder="0" />
                            </div>
                        </div>
                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 0.50:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="moneda050" placeholder="0" />
                            </div>
                        </div>
                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 0.20:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="moneda020" placeholder="0" />
                            </div>
                        </div>
                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 0.10:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="moneda010" placeholder="0" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <template #footer>
            <div class="contenedor-footer">
                <div class="contenedor-button-footer">
                    <Button label="Cerrar" icon="pi pi-times" class="p-button-sm p-button-raised p-button-danger" @click="ocultarCajaDialog"/>
                </div>
                <div class="contenedor-button-footer">
                    <Button label="Guardar" icon="pi pi-check" class="p-button-sm p-button-raised p-button-success" @click="registrarCaja()" />
                </div>
            </div>
        </template>
    </Dialog>


    <Dialog
        :visible.sync="nuevoDepositoDialog"
        :modal="true"
        :closable="false"
        class="contenedor-dialog fluid"
        position="center" >
        <template #header>
            <div class="sidebar-header">
                <i class="pi pi-file sidebar-icon"></i>
                <h4 class="sidebar-title">DEPOSITOS EXTRAS</h4>
            </div>
        </template>
        

        <div class="p-inputgroup">
            <span class="p-inputgroup-addon">
                <i class="fa fa-money"></i>
            </span>
            <span class="p-float-label">
                <InputNumber class="p-inputnumber-sm" v-model="depositos" mode="currency" currency="BOB" locale="es-BO"/>
                <label for="precio">SALDO DEPOSITO:</label>
            </span>
        </div>

        <br>

        <div class="p-inputgroup">
            <span class="p-inputgroup-addon">
                <i class="fa fa-money"></i>
            </span>
            <span class="p-float-label">
                <InputText class="p-inputtext-sm" v-model="Desdepositos" placeholder="Descripcion de deposito""/>
            </span>
        </div>

        <template #footer>
            <div class="contenedor-footer">
                <div class="contenedor-button-footer">
                    <Button label="Cerrar" icon="pi pi-times" class="p-button-sm p-button-raised p-button-danger" @click="ocultarDepositoDialog"/>
                </div>
                <div class="contenedor-button-footer">
                    <Button label="Guardar" icon="pi pi-check" class="p-button-sm p-button-raised p-button-success" @click="depositar()" />
                </div>
            </div>
        </template>
    </Dialog>


    <Dialog
        :visible.sync="nuevaSalidaDialog"
        :modal="true"
        :closable="false"
        class="contenedor-dialog fluid"
        position="center">
        <template #header>
            <div class="sidebar-header">
                <i class="pi pi-file sidebar-icon"></i>
                <h4 class="sidebar-title">SALIDAS EXTRAS</h4>
            </div>
        </template>
        

        <div class="p-inputgroup">
            <span class="p-inputgroup-addon">
                <i class="fa fa-money"></i>
            </span>
            <span class="p-float-label">
                <InputNumber class="p-inputnumber-sm" v-model="salidas" mode="currency" currency="BOB" locale="es-BO"/>
                <label for="precio">SALDO SALIDA:</label>
            </span>
        </div>

        <br>

        <div class="p-inputgroup">
            <span class="p-inputgroup-addon">
                <i class="fa fa-money"></i>
            </span>
            <span class="p-float-label">
                <InputText class="p-inputtext-sm" v-model="Dessalidas" placeholder="Descripcion de salida"/>
            </span>
        </div>

        <template #footer>
            <div class="contenedor-footer">
                <div class="contenedor-button-footer">
                    <Button label="Cerrar" icon="pi pi-times" class="p-button-sm p-button-raised p-button-danger" @click="ocultarSalidaDialog"/>
                </div>
                <div class="contenedor-button-footer">
                    <Button label="Guardar" icon="pi pi-check" class="p-button-sm p-button-raised p-button-success" @click="retirar()" />
                </div>
            </div>
        </template>
    </Dialog>


    <Dialog
        :visible.sync="nuevaVerDialog"
        :modal="true"
        :closable="false"
        class="contenedor-dialog fluid"
        :contentStyle="{ overflow: 'auto', maxHeight: '90vh', maxWidth: '90vw' }"
        position="center">
        <template #header>
            <div class="sidebar-header">
                <i class="pi pi-file sidebar-icon"></i>
                <h4 class="sidebar-title">TRANSACCIONES CAJA</h4>
            </div>
        </template>

        <TabView>
            <TabPanel header="Ventas Realizadas">
                    <TransaccionIngreso v-if="ingreso" :data="ingreso" />
            </TabPanel>
            <TabPanel header="Transacciones Extras">
                    <TransaccionExtra v-if="extra" :data="extra" />
            </TabPanel>
        </TabView>

        <template #footer>
            <div class="contenedor-footer">
                <div class="contenedor-button-footer">
                    <Button label="Cerrar" icon="pi pi-times" class="p-button-sm p-button-raised p-button-danger" @click="ocultarVerDialog"/>
                </div>
            </div>
        </template>
    </Dialog>


     <Dialog
        :visible.sync="nuevaArqueoDialog"
        :modal="true"
        :closable="false"
        class="contenedor-dialog fluid"
        :contentStyle="{ overflow: 'auto', maxHeight: '90vh', maxWidth: '90vw' }"
        position="center">
        <template #header>
            <div class="p-grid p-align-center">
                <div class="p-col">
                    <i class="pi pi-file sidebar-icon"></i>
                    <h4 class="sidebar-title">ARQUEO DE CAJA</h4>
                </div>
                <div class="p-col" style="text-align: right;">
                    <strong>Total Efectivo: {{ totalEfectivo }}</strong>
                </div>
            </div>
        </template>

        <div class="p-fluid">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="p-grid">
                    <div class="p-col-12 p-md-6">
                        <div class="p-grid p-align-center">
                            <div class="p-col">
                                <h4>BILLETES</h4>
                            </div>
                            <div class="p-col" style="text-align: right;">
                                <span class="font-weight-bold">Total = {{ totalBilletes }}</span>
                            </div>
                        </div>

                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 200:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="billete200" placeholder="0" />
                            </div>
                        </div>
                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 100:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="billete100" placeholder="0" />
                            </div>
                        </div>
                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 50:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="billete50" placeholder="0" />
                            </div>
                        </div>
                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 20:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="billete20" placeholder="0" />
                            </div>
                        </div>
                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 10:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="billete10" placeholder="0" />
                            </div>
                        </div>
                    </div>
                    <div class="p-col-12 p-md-6">
                        <div class="p-grid p-align-center">
                            <div class="p-col">
                                <h4>MONEDAS</h4>
                            </div>
                            <div class="p-col" style="text-align: right;">
                                <span class="font-weight-bold">Total = {{ totalMonedas }}</span>
                            </div>
                        </div>

                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 5:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="moneda5" placeholder="0" />
                            </div>
                        </div>
                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 2:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="moneda2" placeholder="0" />
                            </div>
                        </div>
                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 1:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="moneda1" placeholder="0" />
                            </div>
                        </div>
                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 0.50:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="moneda050" placeholder="0" />
                            </div>
                        </div>
                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 0.20:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="moneda020" placeholder="0" />
                            </div>
                        </div>
                        <div class="p-field p-grid">
                            <label class="p-col-12 p-md-4">Bs. 0.10:</label>
                            <div class="p-col-12 p-md-8">
                                <InputNumber v-model="moneda010" placeholder="0" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <template #footer>
            <div class="p-grid p-justify-end">
                <div class="p-col-6 p-md-3">
                    <Button label="Cerrar" icon="pi pi-times" class="p-button-sm p-button-raised p-button-danger" @click="ocultarArqueoDialog"/>
                </div>
                <div class="p-col-6 p-md-3">
                    <Button label="Guardar" icon="pi pi-check" class="p-button-sm p-button-raised p-button-success" @click="registrarArqueo()" />
                </div>
            </div>
        </template>
    </Dialog>
</Panel>

    </main>
</template>

<script>
import TransaccionErgeso from "./Tables/TransaccionEgreso.vue";
import TransaccionIngreso from "./Tables/TransaccionIngreso.vue";
import TransaccionExtra from "./Tables/TransaccionExtra.vue";
import jsPDF from 'jspdf';
import 'jspdf-autotable';
import axios from 'axios';

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup'; 
import Button from 'primevue/button';
import Chip from 'primevue/chip';
import Toolbar from 'primevue/toolbar';
import InputNumber from 'primevue/inputnumber';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import Panel from 'primevue/panel';
import Tag from 'primevue/tag';



export default {
data (){
    return {
        id: 0,
        idsucursal : 0,
        nombre_sucursal:'',
        idusuario : 0,
        usuario : '',
        fechaApertura : '',
        fechaCierre : '',
        saldoInicial : 0,

        //Depositos extras
        depositos : 0,
        Desdepositos:'',
        
        //Salidas extras
        salidas : 0,
        Dessalidas : '',

        
        ventasContado : '',
        ventasCredito : '',
        comprasContado : '',
        comprasCredito : '',
        saldoFaltante : '',
        PagoCuotaEfectivo : '',
        saldoCaja : '',
        arqueo_id: 0,
        billete200 : 0,
        billete100 : 0,
        billete50 : 0,
        billete20 : 0,
        billete10 : 0,
        totalBilletes: 0,
        moneda5 : 0,
        moneda2 : 0,
        moneda1 : 0,
        moneda050 : 0,
        moneda020 : 0,
        moneda010 : 0,
        totalMonedas : 0,
        arrayCaja : [],
        arrayTransacciones: [],
        ArrayIngresos:[],
        ArrayEgresos:[],
        arrayTransaccionesreporte:[],
        ArrayIngresosreporte:[],
        ArrayCajareporte:[],
        ArrayEmpresa: [],
        egreso: null,
        ingreso: null,
        extra: null,
        modal : 0,
        modal2 : 0,
        modal3 : 0,
        modal4 : 0,
        modal5 : 0,
        totalEfectito: 0,
        tituloModal : '',
        tituloModal2: '',
        tituloModal3: '',
        tituloModal4: '',
        tituloModal5: '',
        tipoAccion : 0,
        arqueoRealizado : false,
        errorCaja : 0,
        errorMostrarMsjCaja : [],
        pagination : {
            'total' : 0,
            'current_page' : 0,
            'per_page' : 0,
            'last_page' : 0,
            'from' : 0,
            'to' : 0,
        },
        offset : 3,
        arraySucursal :[],
        arrayUser :[],
        buscar : '',
        criterio : '',
        mostrarBotonesSecundarios: false,

        //prime vue caja
        nuevoCajaDialog: false,
        nuevoDepositoDialog: false,
        nuevaSalidaDialog: false,
        nuevaVerDialog: false,
        nuevaArqueoDialog: false,

        dialogStyle: {
                width: '80vw',
            },
        //submitted: false,


    }
},
components: {

        DataTable,
        Column,
        ColumnGroup,
        Button,
        Chip,
        Toolbar,
        InputNumber,
        Dialog,
        InputText,
        TabPanel,
        TabView,
        Panel,
        Tag

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

    }, 

    totalEfectivo() {
    let totalEfec = this.totalBilletes + this.totalMonedas;
    /*if(totalEfec>this.saldoCaja){
        alert("No puede registrar más efectivo de lo que se encuentra en Saldo Caja");
        return("Disminuya correctamente su efectivo según lo que tiene en caja");
    }else{
    }*/
    return totalEfec;
  },

  totalEfectivoAbrirCaja() {
    this.saldoInicial = this.totalBilletes + this.totalMonedas;
    return this.saldoInicial;
  }
},

methods : {
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

    abrirDialogCaja() {
        if(this.cajaAbierta()){
            swal(
                'Ya existe una caja abierta!',
                'Por favor realice el cierre de la caja e intente de nuevo.',
                'error'
            )
            return;
        }

        console.log('abriendo modal')
        //this.menu = {};
        //this.submitted = false;
        this.nuevoCajaDialog = true;
        console.log('abriendo:', this.nuevoCajaDialog);

    },

    ocultarCajaDialog() {
            this.nuevoCajaDialog = false;
            this.saldoInicial = 0;
            //this.submitted = false;
        },

    abrirDialogDeposito(id) {
        console.log('abriendo modal Deposito')
        //this.menu = {};
        //this.submitted = false;
        this.id=id;
        this.nuevoDepositoDialog = true;
        console.log('abriendo:', this.nuevoDepositoDialog);
    },

    ocultarDepositoDialog() {
        this.nuevoDepositoDialog = false;
        this.depositos=0;
        this.Desdepositos='';   
        this.id=0;
    },

    abrirDialogSalida(id) {
        console.log('abriendo modal Deposito')
        //this.menu = {};
        //this.submitted = false;
        this.id=id;
        this.nuevaSalidaDialog = true;
        console.log('abriendo:', this.nuevaSalidaDialog);
    },

    ocultarSalidaDialog() {
        this.nuevaSalidaDialog = false;
        this.salidas=0;
        this.Dessalidas='';   
        this.id=0;
    },

    abrirDialogArqueo(id, saldoCaja) {
        console.log('abriendo modal arqueo')
        //this.menu = {};
        //this.submitted = false;
        this.id=id;
        this.saldoCaja=saldoCaja;
        this.nuevaArqueoDialog = true;
        console.log('abriendo:', this.nuevaArqueoDialog);
    },

    ocultarArqueoDialog() {
        this.nuevaArqueoDialog = false;
        this.id=0;
        this.saldoCaja='';
        this.id=0;
    },

    listarCaja (page,buscar,criterio){
        let me=this;
        var url= '/caja?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio;
        axios.get(url).then(function (response) {
            var respuesta= response.data;
            console.log(respuesta);
            me.arrayCaja = respuesta.cajas;
            //me.pagination= respuesta.pagination;
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
        me.listarCaja(page,buscar,criterio);
    },
            registrarCaja(){
                if (this.validarCaja()){
                    return;
                }
                
                let me = this;
                let formData = new FormData();

                //formData.append('idempresa', 1);
                formData.append('saldoInicial', this.saldoInicial);

                axios.post('/caja/registrar', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }

                }).then(function (response) {
                    me.ocultarCajaDialog();
                    me.listarCaja(1,'','id');
                    swal(
                            'Aperturada!',
                            'Caja aperturada de forma satisfactoria!',
                            'success'
                        )
                    
                    me.billete200 = 0;
                    me.billete100 = 0;
                    me.billete50 = 0;
                    me.billete20 = 0;
                    me.billete10 = 0;
                    me.moneda5 = 0;
                    me.moneda2 = 0;
                    me.moneda1 = 0;
                    me.moneda050 = 0;
                    me.moneda020 = 0;
                    me.moneda010 = 0;
                }).catch(function (error) {
                    console.log(error);
                });
                
            },
    
        registrarArqueo(){
        let me = this;
        this.totalEfectito = this.totalEfectivo;
        this.mostrarBotonesSecundarios = true;

        axios.post('/caja/arqueoCaja', {
            'idcaja':this.id,
            'billete200': this.billete200,
            'billete100': this.billete100,
            'billete50': this.billete50,
            'billete20': this.billete20,
            'billete10': this.billete10,
            'moneda5': this.moneda5,
            'moneda2': this.moneda2,
            'moneda1': this.moneda1,
            'moneda050': this.moneda050,
            'moneda020': this.moneda020,
            'moneda010': this.moneda010

            }).then(function (response) {
                console.log(response);
                me.ocultarArqueoDialog();
                    swal(
                        'Información!',
                        'Conteo de dinero registrado satisfactoriamente!',
                        'success'
                    );
                    me.billete200 = 0;
                    me.billete100 = 0;
                    me.billete50 = 0;
                    me.billete20 = 0;
                    me.billete10 = 0;
                    me.moneda5 = 0;
                    me.moneda2 = 0;
                    me.moneda1 = 0;
                    me.moneda050 = 0;
                    me.moneda020 = 0;
                    me.moneda010 = 0;
            }).catch(function (error) {
                console.log(error);
                    me.billete200 = 0;
                    me.billete100 = 0;
                    me.billete50 = 0;
                    me.billete20 = 0;
                    me.billete10 = 0;
                    me.moneda5 = 0;
                    me.moneda2 = 0;
                    me.moneda1 = 0;
                    me.moneda050 = 0;
                    me.moneda020 = 0;
                    me.moneda010 = 0;
            });
    },
    
    depositar(){
        console.log("entro a depositar")
        let me = this;
        axios.put('/caja/depositar',{
            'depositos':this.depositos,
            'id':this.id,
            'transaccion':this.Desdepositos+'  (movimiento de ingreso )',

        }).then(function (response) {
            me.ocultarDepositoDialog();
            me.listarCaja(1,'','id');
            swal(
                'Información!',
                'Transacción de caja registrada satisfactoriamente!',
                'success'
                )
        }).catch(function (error) {
            console.log(error);
        }); 
    },

    retirar(){
        let me = this;

        axios.put('/caja/retirar',{
            'salidas':this.salidas,
            'transaccion':this.Dessalidas+' (movimiento de egreso  )',
            'id':this.id
        }).then(function (response) {
            me.ocultarSalidaDialog();
            me.listarCaja(1,'','id');
            swal(
                'Información!',
                'Transacción de caja registrada satisfactoriamente!',
                'success'
                )
        }).catch(function (error) {
            console.log(error);
        }); 
    },
    calcularTotalBilletes(){
        const billete200 = parseFloat(this.billete200) || 0;
        const billete100 = parseFloat(this.billete100) || 0;
        const billete50 = parseFloat(this.billete50) || 0;
        const billete20 = parseFloat(this.billete20) || 0;
        const billete10 = parseFloat(this.billete10) || 0;

        this.totalBilletes = billete200*200 + billete100*100 + billete50*50 + billete20*20 + billete10*10;
    },

    calcularTotalMonedas(){
        const moneda5 = parseFloat(this.moneda5) || 0;
        const moneda2 = parseFloat(this.moneda2) || 0;
        const moneda1 = parseFloat(this.moneda1) || 0;
        const moneda050 = parseFloat(this.moneda050) || 0;
        const moneda020 = parseFloat(this.moneda020) || 0;
        const moneda010 = parseFloat(this.moneda010) || 0;

        this.totalMonedas = moneda5*5 + moneda2*2 + moneda1*1 + moneda050*0.50 + moneda020*0.20 + moneda010*0.10;
    },
    cerrarCaja(id){

        const total = this.totalEfectito;
        console.log("plata: ", total);

       swal({
        title: 'Esta seguro de cerrar la caja?',
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

            axios.put('/caja/cerrar',{
                'id': id,
                'saldoFaltante':total
            }).then(function (response) {
                me.listarCaja(1,'','id');
                swal(
                'Cerrada!',
                'La caja fue cerrada con éxito.',
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

    cajaAbierta(){
        for (let i = 0; i < this.arrayCaja.length; i++){
            if(this.arrayCaja[i].estado){
                return true;
            }
        }
        return false;
    },
    
    validarCaja(){
        this.errorCaja=0;
        this.errorMostrarMsjCaja =[];

        if (!this.saldoInicial) this.errorMostrarMsjCaja.push("El saldo inicial no puede estar vacío.");

        if (this.errorMostrarMsjCaja.length) this.errorCaja = 1;

        return this.errorCaja;
    },

    generarReportePDF() {
    const doc = new jsPDF({
        orientation: 'portrait',
        unit: 'mm',
        format: [80, 250]
    });

    doc.setProperties({ title: 'REPORTE DE CAJA' });
    const datosEmpresa = this.ArrayEmpresa; // Asumiendo que solo hay un objeto en el array
    const nombreEmpresa = `${datosEmpresa.nombre}`;
    const telefonoEmpresa = `${datosEmpresa.telefono}`;
    const direccionEmpres = `${datosEmpresa.direccion}`;

    //doc.addPage();

    // Título principal
    doc.setFont("helvetica", "bold");
    doc.setFontSize(8);
    doc.text(nombreEmpresa, 40, 10, null, null, "center");
    doc.text(direccionEmpres, 40, 15, null, null, "center");
    doc.text(telefonoEmpresa, 40, 20, null, null, "center");

    // Línea de separación
    doc.setLineWidth(0.5);
    doc.line(10, 22, 70, 22);

    // Fecha y hora
    const fechaHoy = new Date().toLocaleDateString('es-ES');
    doc.setFont("helvetica", "normal");
    doc.setFontSize(7);
    //doc.text(`Fecha: ${fechaHoy}`, 10, 26);

    // Fecha de apertura y cierre
    const datosCaja = this.ArrayCajareporte[0]; // Asumiendo que solo hay un objeto en el array
    const subtitleFontSize = 8; // Tamaño de la fuente para subtítulos y datos ajustado
    let textY = 30;

    const fechaAperturaText = `Fecha de Apertura: ${datosCaja.fechaApertura}`;
    const fechaCierreText = `Fecha de Cierre: ${datosCaja.fechaCierre}`;

    doc.setFont("helvetica", "bold");
    doc.setFontSize(subtitleFontSize);
    doc.text(fechaAperturaText, 10, textY);
    textY += 5;
    doc.text(fechaCierreText, 10, textY);

    // Espacio entre secciones
    textY += 5;

    // Saldo inicial, ventas en efectivo y QR, total de ambas ventas
    const datosCajaArray = [
        ["Saldo Inicial", datosCaja.saldoInicial],
        ["Ventas Efectivo", datosCaja.ventasContado],
        ["Ventas QR", datosCaja.ventasQR],
        ["Total saldo ventas", datosCaja.saldototalVentas]
    ];

    datosCajaArray.forEach(item => {
        textY += 5;
        doc.setFont("helvetica", "bold");
        doc.setFontSize(subtitleFontSize);
        doc.text(`${item[0]}:`, 10, textY);
        doc.setFont("helvetica", "normal");
        doc.text(item[1].toString(), 50, textY);
    });

    // Espacio entre secciones
    textY += 5;

    // Depósitos extras, salidas extras
    const extraMovements = [
        ["Depósitos extras", datosCaja.depositos],
        ["Salidas extras", datosCaja.salidas]
    ];

    extraMovements.forEach(item => {
        textY += 5;
        doc.setFont("helvetica", "bold");
        doc.setFontSize(subtitleFontSize);
        doc.text(`${item[0]}:`, 10, textY);
        doc.setFont("helvetica", "normal");
        doc.text(item[1].toString(), 50, textY);
    });

    // Espacio entre secciones
    textY += 5;

    // Saldo caja, saldo faltante
    const saldoFinalArray = [
        ["Saldo Caja", datosCaja.saldoCaja],
        ["Saldo Faltante", datosCaja.saldoFaltante],
        ["Saldo Sobrante", datosCaja.saldoSobrante]

    ];

    saldoFinalArray.forEach(item => {
        textY += 5;
        doc.setFont("helvetica", "bold");
        doc.setFontSize(subtitleFontSize);
        doc.text(`${item[0]}:`, 10, textY);
        doc.setFont("helvetica", "normal");
        doc.text(item[1].toString(), 50, textY);
    });

    // Espacio entre secciones
    textY += 10;

    // Nombre encargado y firma
    doc.setFont("helvetica", "bold");
    doc.setFontSize(subtitleFontSize);
    doc.text("Nombre encargado: ______________________", 10, textY);
    textY += 10;
    doc.text("Firma: ______________________", 10, textY);

    // Guardar el PDF
    doc.save('reporte_caja.pdf');
},

    /*generarReportePDF() {
        const doc = new jsPDF();

        // Título principal
        const title = "REPORTE DE CAJA";
        const titleFontSize = 24; // Tamaño de la fuente del título principal
        const titleWidth = doc.getStringUnitWidth(title) * titleFontSize / doc.internal.scaleFactor;
        const pageWidth = doc.internal.pageSize.width;
        const titleX = (pageWidth - titleWidth) / 2;
        doc.setFont("helvetica", "bold");
        doc.setFontSize(titleFontSize);
        doc.text(title, titleX, 20);

        // Fecha de apertura y cierre, centradas debajo del título principal
        const datosCaja = this.ArrayCajareporte[0]; // Asumiendo que solo hay un objeto en el array
        const subtitleFontSize = 10; // Tamaño de la fuente para subtítulos y datos
        const textY = 30;

        const fechaAperturaText = `Fecha de Apertura: ${datosCaja.fechaApertura}`;
        const fechaCierreText = `Fecha de Cierre: ${datosCaja.fechaCierre}`;
        const nombreSucursalText = `Sucursal: ${datosCaja.nombreSucursal}`;
        const fechaAperturaWidth = doc.getStringUnitWidth(fechaAperturaText) * subtitleFontSize / doc.internal.scaleFactor;
        const fechaCierreWidth = doc.getStringUnitWidth(fechaCierreText) * subtitleFontSize / doc.internal.scaleFactor;
        const nombreSucursalWidth = doc.getStringUnitWidth(nombreSucursalText) * subtitleFontSize / doc.internal.scaleFactor;


        doc.setFont("helvetica", "bold");
        doc.setFontSize(subtitleFontSize);
        doc.text(fechaAperturaText, (pageWidth - fechaAperturaWidth) / 2, textY);
        doc.text(fechaCierreText, (pageWidth - fechaCierreWidth) / 2, textY + 6);
        doc.text(nombreSucursalText, (pageWidth - nombreSucursalWidth) / 2, textY + 12);


        // Otros datos de caja
        const datosCajaArray = [
            ["Saldo Inicial", datosCaja.saldoInicial],
            ["Ventas Efectivo", datosCaja.ventasContado, "Ventas QR", datosCaja.ventasQR, "Total saldo ventas", datosCaja.saldototalVentas],
            ["Depósitos", datosCaja.depositos],
            ["Salidas", datosCaja.salidas],
            ["Saldo Caja", datosCaja.saldoCaja, "Saldo Faltante", datosCaja.saldoFaltante]
        ];

        // Añadir los datos de caja al PDF
        let yOffset = textY + 16; // Posición inicial Y para los datos de caja
        const dataX = 50; // Posición X para los datos

        datosCajaArray.forEach(item => {
            doc.setFont("helvetica", "bold");
            doc.setFontSize(subtitleFontSize);
            doc.text(item[0] + ":", 14, yOffset);
            doc.setFont("helvetica", "normal");
            doc.text(item[1], dataX, yOffset);

            if (item.length > 2) {
                doc.setFont("helvetica", "bold");
                doc.setFontSize(subtitleFontSize);
                doc.text(item[2] + ":", 85, yOffset);
                doc.setFont("helvetica", "normal");
                doc.text(item[3], 120, yOffset);
            }

            if (item.length > 4) {
                doc.setFont("helvetica", "bold");
                doc.setFontSize(subtitleFontSize);
                doc.text(item[4] + ":", 155, yOffset);
                doc.setFont("helvetica", "normal");
                doc.text(item[5], 190, yOffset);
            }

            yOffset += 8; // Reducir el espacio entre las filas
        });

        // Subtítulo para transacciones
        yOffset += 5; // Añadir espacio antes del siguiente subtítulo
        doc.setFont("helvetica", "bold");
        doc.setFontSize(subtitleFontSize);
        doc.text("Transacciones de Retiro y Depósito", 14, yOffset);

        // Encabezados y datos de la tabla de transacciones
        const transaccionesHeaders = [["ENCARGADO", "TIPO", "MONTO IMPORTE", "FECHA"]];
        const transaccionesData = this.arrayTransaccionesreporte.map(transaccion => [
            transaccion.usuario,
            transaccion.transaccion,
            transaccion.importe,
            transaccion.fecha
        ]);

        // Agregar la tabla de transacciones al PDF
        doc.autoTable({
            head: transaccionesHeaders,
            body: transaccionesData,
            startY: yOffset + 5, // Espacio después del subtítulo
            theme: 'striped',
            headStyles: {
                fillColor: [16, 180, 129] // Cambiar el color de fondo de los encabezados
            },
            margin: { top: 10 },
            styles: { fontSize: 8 } // Reducir el tamaño de la fuente de la tabla
        });

        // Subtítulo para ventas
        doc.setFont("helvetica", "bold");
        doc.setFontSize(subtitleFontSize);
        doc.text("Ventas Realizadas", 14, doc.autoTable.previous.finalY + 10);

        // Encabezados y datos de la tabla de ventas
        const ventasHeaders = [["Estado", "Fecha", "Cliente", "Tipo Pago", "Comprobante", "Total", "Usuario"]];
        const ventasData = this.ArrayIngresosreporte.map(venta => [
            venta.estado,
            venta.fecha_hora,
            venta.nombre,
            venta.nombre_tipo_pago,
            venta.num_comprobante,
            venta.total,
            venta.usuario
        ]);

        // Agregar la tabla de ventas al PDF
        doc.autoTable({
            head: ventasHeaders,
            body: ventasData,
            startY: doc.autoTable.previous.finalY + 15, // Espacio después del subtítulo
            theme: 'striped',
            headStyles: {
                fillColor: [16, 180, 129] // Cambiar el color de fondo de los encabezados
            },
            margin: { top: 10 },
            styles: { fontSize: 8 } // Reducir el tamaño de la fuente de la tabla
        });

        // Guardar el PDF
        doc.save('reporte_caja.pdf');
    },*/

    datosreportecaja(id){
        let me = this;
        var url = '/reportecajapdf?id=' + id;
        axios.get(url).then(function (response) {
            var respuesta = response.data;
            
            console.log(respuesta);
            me.arrayTransaccionesreporte = respuesta.transacciones;
            me.ArrayIngresosreporte = respuesta.ventas;
            me.ArrayCajareporte = respuesta.cajaport;
            me.ArrayEmpresa = respuesta.empresa;
            console.log("esto es caja: ", me.ArrayEmpresa);
            me.generarReportePDF();
        })
        .catch(function (error) {
            console.log(error);
        }); 
    },

    abrirDialogVer(id){
        console.log('abriendo modal ver')
        //this.menu = {};
        //this.submitted = false;
        this.nuevaVerDialog = true;
        this.listarTransacciones(id);

        console.log('abriendo:', this.nuevaVerDialog);
    },

    listarTransacciones(id){
        let me=this;
        var url= '/transacciones/' + id;
        axios.get(url).then(function (response) {
        var respuesta= response.data;
                            
        console.log(respuesta);
        me.arrayTransacciones = respuesta.transacciones.data;
        // me.pagination= respuesta.pagination;
        me.ArrayEgresos=respuesta.ingresos;
        me.ArrayIngresos=respuesta.ventas.data;
        console.log("esto es vnentas: ", me.ArrayIngresos);

        me.egreso = respuesta.ingresos;
        me.ingreso = respuesta.ventas;
        me.extra = respuesta.transacciones;
        })
        .catch(function (error) {
        console.log(error);
        });
    },

    ocultarVerDialog() {
        this.nuevaVerDialog = false;
        this.egreso = null;
        this.ingreso = null;
        this.extra = null;
    },



    cerrarModal(){
        this.modal=0;
        this.tituloModal='';
        this.idsucursal= 0;
        this.sucursal='';
        this.saldoInicial=0;
    },
    
    cerrarModal2(){
        this.modal2=0;
        this.depositos=0;
        this.Desdepositos='';
    },

    cerrarModal3(){
        this.modal3=0;
        this.salidas=0;
        this.Dessalidas='';
    },

    cerrarModal4(){
        this.modal4=0;
        this.egreso = null;
        this.ingreso = null;
        this.extra = null;
    },

    cerrarModal5(){
        this.modal5=0;
    },

    abrirModal(modelo, accion, data = []){
        switch(modelo){
            case "caja":
            {
                switch(accion){
                    case 'registrar':
                    {
                        if(this.cajaAbierta()){
                            swal(
                            'Ya existe una caja abierta!',
                            'Por favor realice el cierre de la caja e intente de nuevo.',
                            'error'
                            )
                            return;
                        }

                        this.modal = 1;
                        this.tituloModal = 'Apertura de Caja Sucursal: ';
                        this.saldoInicial= 0;

                        this.tipoAccion = 1;
                        break;
                    }
                }
            }
        }
    },
    
    abrirModal2(modelo, accion, data = []){
        switch(modelo){
            case "cajaDepositar":
            {
                switch(accion){
                    case 'depositar':
                    {
                        this.modal2 = 1;
                        this.tituloModal2='Depositar Dinero';
                        this.id=data['id'];

                        this.tipoAccion=2;

                        break;
                    }
                }
            }
        }
    },

    abrirModal3(modelo, accion, data = []){
        switch(modelo){
            case "cajaRetirar":
            {
                switch(accion){
                    case 'retirar':
                    {
                        this.modal3 = 1;
                        this.tituloModal3='Retirar Dinero';
                        this.id=data['id'];

                        this.tipoAccion=3;

                        break;
                    }
                }
            }
        }
    },

    abrirModal4(modelo, accion, id){
        switch(modelo){
            case "cajaVer":
            {
                switch(accion){
                    case 'ver':
                    {
                        this.modal4 = 1;
                        this.tituloModal4 = 'Transacciones Caja';

                        let me=this;
                        var url= '/transacciones/' + id;
                        axios.get(url).then(function (response) {
                            var respuesta= response.data;
                            
                            console.log(respuesta);
                            me.arrayTransacciones = respuesta.transacciones.data;
                            // me.pagination= respuesta.pagination;
                            me.ArrayEgresos=respuesta.ingresos;
                            me.ArrayIngresos=respuesta.ventas.data;
                            console.log("esto es vnentas: ", me.ArrayIngresos);


                            me.egreso = respuesta.ingresos;
                            me.ingreso = respuesta.ventas;
                            me.extra = respuesta.transacciones;
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                        
                        break;
                    }
                }
            }
        }
    },

    abrirModal5(modelo, accion, id, saldoCaja){
        switch(modelo){
            case "arqueoCaja":
            {
                switch(accion){
                    case 'contar':
                    {
                        this.modal5 = 1;
                        this.tituloModal5 = 'Arqueo de Caja';
                        this.id=id;
                        this.saldoCaja=saldoCaja;
                        this.tipoAccion = 5;
                        break;
                    }
                }
            }
        }
    }
}, 

watch: {
    'billete200': 'calcularTotalBilletes',
    'billete100': 'calcularTotalBilletes',
    'billete50': 'calcularTotalBilletes',
    'billete20': 'calcularTotalBilletes',
    'billete10': 'calcularTotalBilletes',
    'moneda5': 'calcularTotalMonedas',
    'moneda2': 'calcularTotalMonedas',
    'moneda1': 'calcularTotalMonedas',
    'moneda050': 'calcularTotalMonedas',
    'moneda020': 'calcularTotalMonedas',
    'moneda010': 'calcularTotalMonedas'
},

mounted() {
    this.listarCaja(1,this.buscar,this.criterio);
    this.updateDialogStyle();
    window.addEventListener('resize', this.updateDialogStyle);   
},
beforeDestroy() {
        window.removeEventListener('resize', this.updateDialogStyle);
    }
}
</script>
<style scoped>    
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
    margin-top: 1rem;
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

    .titulo-modal {
    display: flex;
    align-items: center;
    }

    .sidebar-title {
    font-size: 1.2rem;
    margin: 0;
    }

    .contenedor-dialog {
        width: 100%; /* Ajusta el ancho según tus necesidades */
    }

    .titulo-modal {
        display: flex;
        align-items: center;
    }

    .sidebar-icon {
        margin-right: 10px;
    }

    .p-field {
        margin-bottom: 1rem;
    }

    .input-number-wide .p-inputtext {
        width: 100%; /* Ajusta el ancho del InputNumber */
    }


    .p-grid {
        margin: 0;
    }

    .p-col-6 {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .p-col-6.p-d-flex.p-jc-end {
        justify-content: flex-end;
    }

    .contenedor-footer {
        display: flex;
        justify-content: space-between;
        padding: 0 ;
    }

    .contenedor-button-footer button {
        margin: 0 0 0 0;
    }

    .sidebar-header {
        display: flex;
        align-items: center; /* Centrar verticalmente los elementos */
        padding-right: 20px;
    }
    
    .mb-4 {
        margin-top: 0.5rem;
    }

    >>> .p-dialog-header {
        padding: 1rem;
    }

    >>> .p-dialog-content {
        padding: 1rem 1rem 1rem;
    }

    >>> .p-dialog-footer {
        padding: 0 1rem 1rem 1rem;
    }

    >>> .p-dialog-mask.p-component-overlay {
        padding-top: 25px;
    }

</style>
