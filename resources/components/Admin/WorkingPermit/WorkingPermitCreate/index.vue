<template>
    <v-card
        class="trezo-card border-radius d-block bg-white border-0 shadow-none"
    >
        <form>
            <div class="row">
                <div class="col-sm-6">
                    <div class="trezo-form-group">
                        <v-label class="d-block fw-medium text-black">
                            Project Name
                        </v-label>
                        <v-text-field
                            label="Enter project name"
                            placeholder="E.g. Project CyberSphere"
                            required
                        ></v-text-field>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="trezo-form-group">
                        <v-label class="d-block fw-medium text-black">
                            Project ID
                        </v-label>
                        <v-text-field
                            label="Enter project ID"
                            placeholder="E.g. #854"
                            required
                        ></v-text-field>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="trezo-form-group">
                        <v-label class="d-block fw-medium text-black">
                            Start Date
                        </v-label>
                        <v-text-field
                            type="date"
                            placeholder="Choose a date"
                            class="date-input"
                        >
                        </v-text-field>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="trezo-form-group">
                        <v-label class="d-block fw-medium text-black">
                            End Date
                        </v-label>
                        <v-text-field
                            type="date"
                            placeholder="Choose a date"
                            class="date-input"
                        >
                        </v-text-field>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="trezo-form-group">
                        <v-label class="d-block fw-medium text-black">
                            Description
                        </v-label>
                        <CommonTextEditor />
                        <!-- <v-textarea label="Type here...."></v-textarea> -->
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="trezo-form-group">
                        <v-label class="d-block fw-medium text-black">
                            Budget
                        </v-label>
                        <v-text-field
                            label="Enter budget"
                            placeholder="E.g. $90"
                            required
                        ></v-text-field>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="trezo-form-group">
                        <v-label class="d-block fw-medium text-black">
                            Priority Status
                        </v-label>
                        <v-select
                            label="Select"
                            :items="[
                                'In Progress',
                                'Pending',
                                'Completed',
                                'Not Started',
                            ]"
                        >
                        </v-select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="trezo-form-group">
                        <v-label class="d-block fw-medium text-black">
                            Categories
                        </v-label>
                        <v-select
                            label="Select"
                            :items="[
                                'Book',
                                'Watch',
                                'Electronics',
                                'Sports',
                                'Japan',
                                'Fashion',
                                'Jewellery',
                            ]"
                        >
                        </v-select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="trezo-form-group">
                        <v-label class="d-block fw-medium text-black">
                            Project Manager
                        </v-label>
                        <v-select
                            label="Select"
                            :items="[
                                'Walter Frazier',
                                'Kimberly Anderson',
                                'Roscoe Guerrero',
                                'Robert Stewart',
                                'Dustin Fritch',
                                ,
                            ]"
                        >
                        </v-select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="trezo-form-group">
                        <v-label class="d-block fw-medium text-black">
                            Team Members
                        </v-label>
                        <v-select
                            label="Select"
                            :items="[
                                'Sarah Johnson',
                                'Michael Smith',
                                'Emily Brown',
                                'Jason Lee',
                                'Ashley Davis',
                                'Mark Thompson',
                            ]"
                        >
                        </v-select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="trezo-form-group">
                        <v-label class="d-block fw-medium text-black">
                            Project Tags
                        </v-label>
                        <v-select
                            label="Select"
                            :items="[
                                'Book',
                                'Watch',
                                'Electronics',
                                'Sports',
                                'Fashion',
                                'Jewellery',
                            ]"
                        >
                        </v-select>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="trezo-form-group">
                        <v-label class="d-block fw-medium text-black">
                            Project Preview Image
                        </v-label>
                        <FileUploader />
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="trezo-form-group">
                        <v-label class="d-block fw-medium text-black">
                            Attached File
                        </v-label>
                        <FileUploader />
                    </div>
                </div>
            </div>
            <div class="form-btn-box">
                <button type="button" class="warn">Cancel</button>
                <button type="button" class="primary">
                    <i class="material-symbols-outlined"> add </i>
                    Create Seller
                </button>
            </div>
        </form>
    </v-card>
</template>

<script>
import FileUploader from "../../../Common/FileUploader.vue";

export default {
    name: "CreateProject",
    components: {
        FileUploader,
    },
};
</script>
