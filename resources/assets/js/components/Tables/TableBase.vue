<template>
    <main>
        <DataTable 
            :value="items" stripedRows
            :paginator="true" 
            :rows="5" 
            responsiveLayout="scroll"
            paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
            :rowsPerPageOptions="[5, 8, 10]"
            currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
        >
            <Column v-for="field in fields" :key="field.key" :field="field.key" :header="field.label"></Column>
        </DataTable>
        <div v-if="items.length <= 0" class="no-data">
            Parece que aún no has realizado ningún movimiento.
        </div>
    </main>
</template>

<script >
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';


export default {
    props: {

        items: {
            type: Array,
            required: true
        },
        perPage: {
            type: Number,
            required: true
        },
        rows: {
            type: Number,
            required: true
        },
        fields: {
            type: Array,
            required: true
        },
    },
    data() {
        return {
            currentPage: 1,
        }
    },
    beforeUpdate() {
        console.log('beforeUpdate', this.$props);
    },
    created() {
    },
    components: {
        DataTable,
        Column,
    },
    computed: {
    },
    methods: {
    },
    watch: {
    }

}
</script>

