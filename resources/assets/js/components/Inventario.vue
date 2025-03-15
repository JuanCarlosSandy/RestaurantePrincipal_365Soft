<template>
    <main class="main">
        <div class="p-p-4 p-mx-auto" style="max-width: 100%;">

        <Panel header="Menu Completo" style="font-size: 1.5rem;" :toggleable="false">
            <template #header>
                <div class="header-container">
                    <Dropdown
                        v-model="AlmacenSeleccionado"
                        :options="arrayAlmacenes"
                        optionLabel="nombre_almacen"
                        optionValue="id"
                        placeholder="Seleccione"
                        @change="getDatosAlmacen"
                        class="almacen-dropdown"
                    />

                    <SelectButton
                        v-model="tipoSeleccionado"
                        :options="tipoOptions"
                        optionLabel="label"
                        optionValue="value"
                        class="tipo-select"
                    />

                    <InputText v-model="buscar" placeholder="Buscar..." class="search-input" />
                </div>
            </template>

            <DataTable v-if="tipoSeleccionado === 'item'"
               :value="arrayInventario"
               :paginator="true"
               :rows="6"
               :filters="filters"
               responsiveLayout="scroll"
               paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
               :rowsPerPageOptions="[6,8,10]"
               currentPageReportTemplate="Showing {first} to {last} of {totalRecords}">
                <Column field="nombre_producto" header="Producto"></Column>
                <Column field="unidad_paquete" header="Unidad x Paquete"></Column>
                <Column field="saldo_stock_total" header="Stock Unidades"></Column>
                <Column field="stock_paquetes" header="Stock Paquetes"></Column>

            </DataTable>

            <DataTable v-if="tipoSeleccionado === 'lote'"
                    :value="arrayInventario"
                    :paginator="true"
                    :rows="6"
                    :filters="filters"
                    responsiveLayout="scroll"
                    paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                    :rowsPerPageOptions="[6,8,10]"
                    currentPageReportTemplate="Showing {first} to {last} of {totalRecords}">
                <Column field="fecha_ingreso" header="Fecha Ingreso"></Column>
                <Column field="fecha_vencimiento" header="Fecha Vencimiento"></Column>
                <Column field="nombre_producto" header="Producto"></Column>
                <Column field="precio_costo_unid" header="Costo Unidad"></Column>
                <Column field="saldo_stock" header="Stock Unidades"></Column>
                <Column field="unidad_paquete" header="Unidad x Paquete"></Column>
                <Column field="stock_paquetes" header="Stock Paquetes"></Column>

            </DataTable>
        </Panel>
    </div>

    </main>
</template>




<script>
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dropdown from 'primevue/dropdown';
import Panel from 'primevue/panel';
import SelectButton from 'primevue/selectbutton';
import InputText from 'primevue/inputtext';
import { FilterMatchMode } from 'primevue/api';

export default {
    data (){
        return {
            arrayInventario: [],
            arrayAlmacenes: [],
            AlmacenSeleccionado: 1,
            idalmacen: 0,
            tipoSeleccionado: 'item',
            buscar: '',

            tipoOptions: [
                { label: 'Por Item', value: 'item' },
                { label: 'Por Lote', value: 'lote' }
            ],

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
            filters: {
                global: { value: '', matchMode: FilterMatchMode.CONTAINS }
            }
        }
    },
    components: {
        DataTable,
        Column,
        Dropdown,
        Panel,
        SelectButton,
        InputText
    },
    watch: {
        tipoSeleccionado(newVal, oldVal) {
            this.cambiarTipo();
        },
        buscar(newVal, oldVal) {
            this.filters.global.value = newVal;
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
        cambiarPagina(page,buscar,criterio){
            let me = this;
            //Actualiza la página actual
            me.pagination.current_page = page;
            //Envia la petición para visualizar la data de esa página
            me.listarInventario(page,buscar,criterio);
        },
        //---------------------------------------
        listarInventario (){
            let me=this;
            let url = '/inventarios/itemLote/' + me.tipoSeleccionado + '?&idAlmacen=' + me.almacenSeleccionado + '&buscar=' + me.buscar + '&criterio=' + me.criterio;
            axios.get(url).then(function (response) {
                var respuesta= response.data;
                console.log("ARRAy:",respuesta);
                me.arrayInventario = respuesta.inventarios;
                console.log('LLEGA:',me.arrayInventario);
                //me.pagination= respuesta.pagination;
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        selectAlmacen() {
            let me = this;
            var url = '/almacen/selectAlmacen';
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                me.arrayAlmacenes = respuesta.almacenes;
                console.log('ALMACEN:',me.arrayAlmacenes);
            })
            .catch(function (error) {
                console.log(error);
            });
        },
        getDatosAlmacen() {
            let me = this;
            if (me.AlmacenSeleccionado !== '') {
                me.loading = true;
                me.almacenSeleccionado = me.AlmacenSeleccionado; // Almacenar el valor seleccionado
                me.idalmacen = Number(me.AlmacenSeleccionado);
                console.log('IDalmacen: ' + me.idalmacen);
                me.listarInventario();
            }
        },
        cambiarTipo() {
            this.getDatosAlmacen(); // Actualizar datos de almacén
        },
        //--------------------------------------
    },
    mounted() {
        this.getDatosAlmacen();
        this.listarInventario();
        this.selectAlmacen();
    },
}
</script>

<style scoped>
    .header-container {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .almacen-dropdown {
        width: 200px;
        font-size: 0.875rem;
        margin-right: 10px;
    }

    .tipo-select {
        width: 250px;
        font-size: 1rem;
        margin-right: 10px;
    }

    .search-input {
        width: 200px;
        font-size: 0.875rem;
    }

    @media (max-width: 768px) {
        .header-container {
            flex-wrap: wrap;
        }

        .almacen-dropdown,
        .tipo-select,
        .search-input {
            width: 100%;
            margin-right: 0;
            margin-bottom: 10px;
        }
    }
</style>
