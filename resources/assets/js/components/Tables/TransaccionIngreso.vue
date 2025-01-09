<template>
    <div class="table-responsive">
        <TableBase :items="items" :rows="rows" :per-page="perPage" :fields="fields"/>
    </div>
</template>

<script>
import TableBase from './TableBase.vue';

export default {
    props: {
        data: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            perPage: 0,
            currentPage: 1,
            fields: [
                { key: 'num_comprobante', label: 'Número Comprobante' },
                { key: 'fecha_hora', label: 'Fecha' },
                { key: 'total', label: 'Importe Bs.' },
                { key: 'nombre_tipo_pago', label: 'Tipo Pago' },
                { key: 'usuario', label: 'Vendedor' }
            ],
            items: []
        };
    },
    created() {
        this.items = this.data.data;
        this.perPage = this.data.per_page;
    },
    components: {
        TableBase
    },
    computed: {
        rows() {
            return this.data.total;
        }
    }
};
</script>
