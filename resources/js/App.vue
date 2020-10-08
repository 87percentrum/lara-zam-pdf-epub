<template>

<v-app>
    <form action="" id="form">
    <v-app-bar color="teal" dark flat fixed>
        <v-card-title>PDF to EPUB Converter </v-card-title>
    </v-app-bar>
    <v-container id="main-container">
        <v-row>
            <v-col sm="12">
                <v-spacer/>
            </v-col>
        </v-row>

        <v-row>
            <v-col sm="12">
                <v-card flat>
                    <v-card-text>Upload a PDF file for conversion by clicking the button below</v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <v-row>
            <v-col sm="12">
                <v-file-input
                    :disabled="disabled"
                    :loading="loading"
                    :label="label"
                    flat id="theFile"
                    accept=".pdf"
                    @change="startProcess"
                    v-model="file"/>
            </v-col>
        </v-row>
    </v-container>

        <v-alert dense prominent type="error" :value="error" dismissible>UPLOAD A VALID PDF</v-alert>

        <v-chip class="ma-2" x-large link label color="pink" dark @click="downloadFile" :active="candownload">
            <jam-download/> DOWNLOAD
        </v-chip>
    </form>
</v-app>

</template>
<script>

export default {

    components: {},

    data: () =>({
        candownload: false,
        file: [],
        converted: null,
        loading: false,
        disabled: false,
        error: false,
        label: "Select a PDF file...",
        defaults : {
            candownload: false,
            label: "Select a PDF file...",
            loading: false,
            disabled: false,
            error: false,
            file: [],
            converted: null
        }
    }),
    methods:  {
        downloadFile() {
            window.location = '/files/' + this.converted;
        },
        resetUploader(){
            this.loading = this.defaults.loading;
            this.disabled = this.defaults.disabled;
            this.error = this.defaults.error;
            this.file = this.defaults.file;
            this.label = this.defaults.label;
        },
        uploadFile() {
            let data = new FormData;
            data.append('file',this.file);

            axios
            .post('/api/file',data,{ headers: {'Content-Type':'multipart/form-data'} })
            .then( response => {
                this.converted = response.data.file;
                this.candownload = true;
            }).finally(() => {
                this.resetUploader();
            });
        },
        startProcess() {
            this.candownload = this.defaults.candownload;
            if( this.file.length === 0 )
                return false;

            this.loading = this.disabled = true;
            this.error = false;
            this.label = "Uploading your PDF now...";

            if( this.file.type !== 'application/pdf' ) {
                this.loading = this.disabled = false;
                this.label = this.defaults.label;
                this.error = true;

                return false;
            }

            this.uploadFile();
        }
    }

}

</script>
