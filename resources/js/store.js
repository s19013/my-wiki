import { createStore } from 'vuex'

export default createStore({
	state: {
        lang:"en",
        globalLoading:false,
        someDialogOpening:false,
        pageName:null,
	},
    getters:{},
	mutations: {
        setLang(state,lang){
            state.lang = lang
        },
        setGlobalLoading(state,loading){
            state.globalLoading = loading
        },
        switchGlobalLoading(state){
            state.globalLoading = !state.globalLoading
        },
        setSomeDialogOpening(state,status){
            state.someDialogOpening = status
        },
        switchSomeDialogOpening(state){
            state.someDialogOpening = !state.someDialogOpening
        },
        setPageName(state,name){
            state.pageName = name
            console.log("Global:" +  state.pageName);
        }
    },
    actions: {
    },
    modules: {
    },
})
