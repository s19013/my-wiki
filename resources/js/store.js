import { createStore } from 'vuex'

export default createStore({
	state: {
        lang:"en",
        globalLoading:false,
        someDialogOpening:false
	},
    getters:{},
	mutations: {
        setLang(state,lang){
            state.lang = lang
        },
        setGlobalLoading(state,loading){
            state.globalLoading = loading
            console.log("Global:" + state.globalLoading);
        },
        switchGlobalLoading(state){
            state.globalLoading = !state.globalLoading
            console.log("Global:" + state.globalLoading);
        },
        setSomeDialogOpening(state,status){
            state.someDialogOpening = status
        },
        switchSomeDialogOpening(state){
            state.someDialogOpening = !state.someDialogOpening
        }
    },
    actions: {
    },
    modules: {
    },
})
