<template>
    <!--begin::Content-->
	<div id="kt_app_content" class="app-content flex-column-fluid">
		<!--begin::Content container-->
		<div id="kt_app_content_container" class="app-container container-fluid">
            <div class="mt-5">
                <div class="card border-0 rounded shadow-sm">
                    <div class="card-header p-5">
                        <!--begin::Page title-->
                        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                            <!--begin::Title-->
                            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Manage User</h1>
                            <!--end::Title-->
                        </div>
                        <!--end::Page title-->
                        <!--begin::Actions-->
                        <div class="d-flex align-items-center gap-2 gap-lg-3">
                        </div>
                        <!--end::Actions-->
                    </div>
                    <div class="card-body p-10">
                        <DxDataGrid
                            ref="gridContainer"
                            :data-source="dataSource"
                            :show-borders="true"
                            :show-row-lines="true"
                            :show-column-lines="true"
                            :selection="{ mode: 'single' }"
                            :hover-state-enabled="true"
                            :remote-operations="remoteOperations"
                            :word-wrap-enabled="true"
                            key-expr="id"
                        >
                            <DxColumn
                            data-field="name"
                            data-type="string"
                            caption="Nama"
                            />
                            <DxColumn
                            data-field="username"
                            data-type="string"
                            caption="Username / NPK"
                            />
                            <DxColumn
                                data-field="kode_unitkerja"
                                caption="Unit Kerja"
                                :filter-operations="allowedOperations"
                                v-model:selected-filter-operation="selectedOperation"
                                :allow-filtering="false"
                            >
                                <DxLookup
                                    :data-source="dataUnitkerja"
                                    value-expr="kode_unitkerja"
                                    display-expr="desc_unitkerja"
                                />
                            </DxColumn>
                            <DxColumn
                            data-field="email"
                            data-type="string"
                            caption="Email"
                            />
                            <DxPaging :page-size="12"/>
                            <DxPager
                            :show-page-size-selector="true"
                            :allowed-page-sizes="[8, 12, 20]"
                            />
                            <DxFilterRow :visible="true"/>
                            <DxHeaderFilter :visible="true"/>
                            <DxGroupPanel :visible="true"/>
                            <DxEditing
                            :allow-adding="true"
                            :allow-updating="true"
                            :allow-deleting="true"
                            mode="row"
                            />
                            <DxGrouping :auto-expand-all="false"/>
                            <DxToolbar>
                                <DxItem
                                    location="before"
                                    name="groupPanel"
                                />
                                <DxItem
                                    location="after"
                                    template="refreshTemplate"
                                />
                                <DxItem
                                    name="addRowButton"
                                />
                                <DxItem
                                    name="exportButton"
                                />
                            </DxToolbar>
                            <template #refreshTemplate>
                                <DxButton
                                    icon="refresh"
                                    @click="refreshDataGrid"
                                />
                            </template>
                        </DxDataGrid>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
</template>

<script>
import {defineComponent, onMounted, ref} from "vue";
//import layout
import LayoutApp from '../../../layout/App.vue'

import axios from "axios";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
// import { onMounted, ref } from "@vue/runtime-core";

import {
    DxDataGrid,
    DxColumn,
    DxEditing,
    DxFilterRow,
    DxHeaderFilter,
    DxGroupPanel,
    DxGrouping,
    DxScrolling,
    DxSummary,
    DxLookup,
    DxTotalItem,
    DxGroupItem,
    DxMasterDetail,
    DxStringLengthRule,
    DxRequiredRule,
    DxRangeRule,
    DxValueFormat,
    DxToolbar,
    DxItem
} from 'devextreme-vue/data-grid';
import CustomStore from 'devextreme/data/custom_store';
import { DxButton } from 'devextreme-vue/button';

    export default defineComponent({

        //layout
        layout: LayoutApp,
        name: "User",
        //register Link di component
        components: {
            DxDataGrid,
            DxColumn,
            DxEditing,
            DxFilterRow,
            DxHeaderFilter,
            DxGroupPanel,
            DxGrouping,
            DxScrolling,
            DxSummary,
            DxLookup,
            DxTotalItem,
            DxGroupItem,
            DxMasterDetail,
            DxStringLengthRule,
            DxRequiredRule,
            DxRangeRule,
            DxValueFormat,
            DxToolbar,
            DxItem,
            DxButton
        },
        //props
        props: {
            // posts: Array // <- nama props yang dibuat di controller saat parsing data
        },
        //define Composition Api
        setup() {
            onMounted(async () => {
                getDataUnitkerja();
            });

            const dataGridAction = ref("index");
            const remoteOperations = ref({
                paging: true,
                filtering: true,
                sorting: true,
                grouping: true,
            });

            const allowedOperations= ['contains', '='];
            const selectedOperation= 'contains';

            const dataUnitkerja = ref();
            const getDataUnitkerja = () => {
                return axios
                    .get('/dataunitkerja')
                    .then(response => {
                        // dataUnitkerja.value = response.data;
                        dataUnitkerja.value = {
                            store: {
                                type: 'array',
                                data: [
                                    { kode_unitkerja: 'D001', desc_unitkerja: 'Not Started' },
                                    { kode_unitkerja: 'D002', desc_unitkerja: 'Need Assistance' },
                                    { kode_unitkerja: 'D003', desc_unitkerja: 'In Progress' },
                                    // ...
                                ],
                                key: 'kode_unitkerja'
                            },
                            pageSize: 10,
                            paginate: true   
                        }
                    })
                    .catch(function (error) {
                        console.error(error);
                        throw new Error('Data Error');
                    });
            };

            function handleErrors(response) {
                if (!response.ok) {
                    throw Error(response.statusText);
                }
                return response;
            }

            const gridContainer = ref();
            const is_exporting = false;
            const dataSource = new CustomStore({
                key: 'id',
                load: function (loadOptions) {
                    const args = {};
                    [
                        "skip",
                        "take",
                        "requireTotalCount",
                        "requireGroupCount",
                        "sort",
                        "filter",
                        "totalSummary",
                        "group",
                        "groupSummary",
                    ];
                    if (loadOptions.sort) {
                        //console.log(loadOptions.sort);
                        args.orderby = loadOptions.sort[0].selector;
                        if (loadOptions.sort[0].desc) args.sort = "desc";
                        if (loadOptions.sort[0].desc == false) args.sort = "asc";
                    }
                    if (!is_exporting) {
                        args.skip = loadOptions.skip || 0;
                        args.take = loadOptions.take || 10;
                    }
                    args.filter = loadOptions.filter;
                    return axios
                    .post("/api/getuser", args)
                    .then((response) => ({
                        data: response.data.data,
                        totalCount: response.data.totalCount,
                        summary: response.data.summary,
                        groupCount: response.data.groupCount,
                    }))
                    .catch(function (error) {
                        console.error(error);
                        throw new Error('Data Loading Error');
                    });
                }.bind(this),
                insert: (values) => {
                    const data = values;
                    data._token = null;
                    return axios
                    .post("/user/store", data)
                    .then((response) => ({
                        response
                    }))
                    .catch(function (error) {
                        console.error(error);
                        throw new Error('Data Loading Error');
                    });
                },
                update: (key, values) => {
                    const data = {};
                    data.key = key;
                    data.data = values;
                    data._token = null;
                    return axios
                    .put(`/user/update/${key}`, data)
                    .then((response) => ({
                        response
                    }))
                    .catch(function (error) {
                        console.error(error);
                        throw new Error('Data Loading Error');
                    });
                },
                remove: (key) => {
                    const data = {};
                    data.key = key;
                    data._token = null;
                    return axios
                    .delete(`/user/delete/${key}`, data)
                    .then((response) => ({
                        response
                    }))
                    .catch(function (error) {
                        console.error(error);
                        throw new Error('Data Loading Error');
                    });
                },
                onBeforeSend: (method, ajaxOptions) => {
                    ajaxOptions.xhrFields = { withCredentials: true };
                },
            });

            const refreshDataGrid = () => {
                gridContainer.value.instance.refresh();
            };

            const isNotEmpty = (value) => {
                return value !== undefined && value !== null && value !== "";
            };

            return {
                dataUnitkerja,
                remoteOperations,
                allowedOperations,
                selectedOperation,
                handleErrors,
                refreshDataGrid,
                dataSource,
                gridContainer
            }
        }
    });
</script>

<style>

</style>