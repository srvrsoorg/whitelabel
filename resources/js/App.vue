<template>
    <vue-progress-bar :class="progressbarClass"></vue-progress-bar>
    <component :is="'style'" v-if="Object.keys(siteSettings).length && siteSettings.color_palette">
        :root{
            --color50: {{siteSettings.color_palette['50']}};
            --color100: {{siteSettings.color_palette['100']}};
            --color200: {{siteSettings.color_palette['200']}};
            --color300: {{siteSettings.color_palette['300']}};
            --color400: {{siteSettings.color_palette['400']}};
            --color500: {{siteSettings.color_palette['500']}};
            --color600: {{siteSettings.color_palette['600']}};
            --color700: {{siteSettings.color_palette['700']}};
            --color800: {{siteSettings.color_palette['800']}};
            --color900: {{siteSettings.color_palette['900']}};
            --selectBg: {{isLightColor ? siteSettings.color_palette['600'] : siteSettings.color_palette['500']}}
        }
    </component>
	<component :is="currentLayout">
        <RefreshPage></RefreshPage> 
        <router-view></router-view>
    </component>
</template>

<script>
import RefreshPage from '@/components/RefreshPage.vue'
import { getCurrentInstance } from "vue";

export default {
    components:{
        RefreshPage
    },
    data(){
        return{
            ins: getCurrentInstance(),
        }
    },
    computed: {
        // Compute the current layout based on the route's metadata or use 'div' as a default
        currentLayout(){
            return this.$route.meta.layout || 'div'
        },
        progressbarClass() {
            return this.ins.appContext.provides.RADON_LOADING_BAR.options
                .canSuccess
                ? "success-bar"
                : "error-bar";
        },
    }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>