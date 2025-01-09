<template>
    <div class="table-responsive">
        <TableBase :items="items" :rows="rows" :per-page="perPage" :fields="fields" />
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
                { key: 'fecha', label: 'Fecha' },
                { key: 'transaccion', label: 'Transacción' },
                { key: 'importe', label: 'Importe Bs.' },
                { key: 'usuario', label: 'Usuario' }
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
