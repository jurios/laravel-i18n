<template>
    <div class="flash-alerts">
        <div class="">
            <transition name="fade">
                <div class="alert alert-flash" role="alert" v-show="show" :class="'alert-' + level">
                    <button type="button" class="close" v-on:click="dismiss(0)"></button>
                    <div class="container text-center">
                        <i class="fas fa-bell mr-2" aria-hidden="true" v-if="icon && level === 'info'"></i>
                        <i class="fas fa-check-circle mr-2" aria-hidden="true" v-if="icon && level === 'success'"></i>
                        <i class="fas fa-exclamation-triangle mr-2" aria-hidden="true" v-if="icon && level === 'warning'"></i>
                        <i class="fas fa-exclamation-circle mr-2" aria-hidden="true" v-if="icon && level === 'danger'"></i>
                        <span v-html="message"></span>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                message: '',
                level: 'info',
                show: false,
                dismissible: true,
                icon: true
            }
        },

        created() {
            window.events.$on(
                'flash', (message, level, icon, dismissible) => {
                    this.flash(message, level, icon, dismissible)
                }
            );
        },

        methods: {
            flash(message, level = 'info', icon = true, dismissible = true) {
                this.show = true;
                this.message = message;
                this.level = level;
                this.icon = icon;
                this.dismissible = dismissible;

                if (this.dismissible) {
                    this.dismiss(3000);
                }
            },

            dismiss(timeout = 0) {
                setTimeout(() => {
                    this.show = false;
                }, timeout);
            }
        }
    }
</script>

<style>

    .flash-alerts {
        position:fixed;
        top: 100px;
        right: 20px;
        z-index: 1000;
    }

    .alert {
        box-shadow: 0px 0px 3px 0px rgba(0,0,0,1);
    }

    .fade-enter-active, .fade-leave-active {
        transition: opacity .5s;
    }
    .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
        opacity: 0;
    }
</style>