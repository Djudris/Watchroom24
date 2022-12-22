import "./app/publicPath";
import Vue from "vue";
import Vuex from "vuex";
import NotificationService from "./app/services/NotificationService";
import TranslationService from "./app/services/TranslationService";
import { createApp, beforeCreate } from "./app";
import "custom-event-polyfill";
import { initClientListeners, initClientStore, createStore } from "./app/store";
import { initListener } from "./app/services/ApiService";
import { mount } from "./mount";
import "./app/main";

window.onload = (event) =>
{
    console.log("Page Loaded");
    Vue.prototype.$mount = mount;

    // defines if the render location is the client
    App.isSSR = false;
    App.isSSREnabled = App.config.log.performanceSsr;

    beforeCreate();

    window.createApp = (selector) =>
    {
        // client-specific bootstrapping logic...
        const app = createApp({
            template: "#ssr-script-container"
        }, store);

        app.$mount(selector, true);
        window.vueApp = app;

        initListener();

        initClientListeners(store);
        initClientStore(store);
    };

    const store = createStore();

    if (window.__INITIAL_STATE__)
    {
        store.replaceState(window.__INITIAL_STATE__);
    }

    window.Vue = Vue;
    window.Vuex = Vuex;
    window.NotificationService = NotificationService;
    window.ceresTranslate = TranslationService.translate;
    window.vueEventHub = new Vue();
    window.ceresStore = store;
    window.vueEventHub = new Vue();
    if (App.config.log.checkSyntax)
    {
        const rootElement = document.getElementById("vue-app");

        rootElement.innerHTML = rootElement.innerHTML.replace(/(?:^|\s)(?::|v-bind:)\S+=(?:""|'')/g, "");
        window.vueApp = new Vue({
            store: window.ceresStore
        });
        vueApp.$mount(rootElement.cloneNode(true));
        if (vueApp.$el.id === "vue-app")
        {
            document.body.replaceChild(vueApp.$el, rootElement);
        }
    }
    else
    {
        // eslint-disable-next-line no-unused-vars
        window.vueApp = new Vue({
            el: "#vue-app",
            store: window.ceresStore
        });
    }
    window.createApp("#vue-app");

    jQuery.event.special.touchstart = {
        // eslint-disable-next-line id-length
        setup: function(_, ns, handle)
        {
            this.addEventListener("touchstart", handle, { passive: !ns.includes("noPreventDefault") });
        }
    };
    jQuery.event.special.touchmove = {
        // eslint-disable-next-line id-length
        setup: function(_, ns, handle)
        {
            this.addEventListener("touchmove", handle, { passive: !ns.includes("noPreventDefault") });
        }
    };
    jQuery.event.special.wheel = {
        setup: function(_, ns, handle)
        {
            this.addEventListener("wheel", handle, { passive: true });
        }
    };
    jQuery.event.special.mousewheel = {
        setup: function(_, ns, handle)
        {
            this.addEventListener("mousewheel", handle, { passive: true });
        }
    };

    if ("ontouchstart" in document.documentElement)
    {
        document.body.classList.add("touch");
    }
    else
    {
        document.body.classList.add("no-touch");
    }

};

