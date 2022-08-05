import{l as E}from"./BaseLayout.4ba1edf1.js";import{l as n,b as d,d as f,g as t,h as s,x as h,e as c,w as m,y as k,v as p,F as N,z as D,k as v,j as i,p as V,m as B,i as A,t as R}from"./app.6c1b8228.js";import{_ as U}from"./plugin-vue_export-helper.21dcd24c.js";const z={data(){return{tagToSearch:"",newTag:"",onlyCheckedFlag:!1,createNewTagFlag:!1,tagDialogFlag:!1,tagSerchLoading:!1,newTagSending:!1,newTagErrorFlag:!1,tagAlreadyExistsErrorFlag:!1,checkedTagList:[],tagSearchResultList:[]}},props:{originalCheckedTag:{type:Array,default:null},searchOnly:{type:Boolean,default:!1}},components:{loading:E},methods:{createNewTagCheck:_.debounce(_.throttle(async function(){if(this.newTag==""){this.newTagErrorFlag=!0;return}else this.createNewTag()},100),150),createNewTag(){this.newTagSending=!0,axios.post("/api/tag/store",{tag:this.newTag}).then(o=>{this.tagToSearch="",this.searchTag(),this.createNewTagFlag=!1,this.newTag="",this.tagAlreadyExistsErrorFlag=!1,this.newTagErrorFlag=!1}).catch(o=>{o.response.status==400&&(this.tagAlreadyExistsErrorFlag=!0)}),this.newTagSending=!1},searchTag:_.debounce(_.throttle(async function(){this.tagSerchLoading=!0,this.tagSearchResultList=[],await axios.post("/api/tag/search",{tag:this.tagToSearch}).then(o=>{for(const e of o.data)this.tagSearchResultList.push({id:e.id,name:e.name});this.tagSerchLoading=!1})},100),150),clearAllCheck(){this.checkedTagList=[]},createNewTagFlagSwitch(){this.createNewTagFlag=!this.createNewTagFlag},tagDialogFlagSwithch(){this.tagDialogFlag=!this.tagDialogFlag,this.createNewTagFlag=!1,this.newTag="",this.tagToSearch="",this.tagAlreadyExistsErrorFlag=!1,this.newTagErrorFlag=!1,this.onlyCheckedFlag=!1,this.tagDialogFlag==!0&&this.searchTag()},serveCheckedTagListToParent(){var o=[];for(const e of this.checkedTagList)o.push(e.id);return o}},watch:{onlyCheckedFlag:function(){this.onlyCheckedFlag==!0?this.tagSearchResultList=this.checkedTagList:this.onlyCheckedFlag==!1&&this.tagDialogFlag==!0&&this.searchTag()}},mounted(){if(this.originalCheckedTag!=null&&this.originalCheckedTag[0].id!=null)for(const o of this.originalCheckedTag)this.checkedTagList.push({id:o.id,name:o.name})}},I=o=>(V("data-v-517c05f9"),o=o(),B(),o),M=i("mdi-tag"),O=i(" \u30BF\u30B0 "),j={class:"Dialog tagDialog"},P={class:"clooseButton"},q=i("mdi-close-box"),G=i("\u9589\u3058\u308B "),H={class:"searchArea"},J=i("mdi-magnify"),K=i(" \u691C\u7D22 "),Q=i(" \u30C1\u30A7\u30C3\u30AF\u3092\u3059\u3079\u3066\u5916\u3059 "),W=I(()=>c("label",{for:"checked"},"\u30C1\u30A7\u30C3\u30AF\u304C\u3064\u3044\u3066\u3044\u308B\u30BF\u30B0\u3060\u3051\u3092\u8868\u793A",-1)),X=["id","value"],Y=["for"],Z={key:0},$=i("mdi-tag-plus"),ee=i(" \u65B0\u898F\u4F5C\u6210 "),te={class:"areaCreateNewTag"},ae={key:0,class:"error"},le={key:1,class:"error"},se=i("mdi-content-save"),oe=i(" \u4F5C\u6210 ");function ie(o,e,F,ne,a,r){const u=n("v-icon"),g=n("v-btn"),w=n("v-text-field"),T=n("v-col"),y=n("v-row"),C=n("loading"),S=n("v-list-item"),b=n("v-list"),x=n("v-dialog");return d(),f("div",null,[t(g,{class:"longButton",color:"submit",size:"small",onClick:h(r.tagDialogFlagSwithch,["stop"])},{default:s(()=>[t(u,null,{default:s(()=>[M]),_:1}),O]),_:1},8,["onClick"]),t(x,{modelValue:a.tagDialogFlag,"onUpdate:modelValue":e[7]||(e[7]=l=>a.tagDialogFlag=l),scrollable:"",persistent:""},{default:s(()=>[c("section",j,[c("div",P,[t(g,{color:"#E57373",size:"small",elevation:"2",onClick:e[0]||(e[0]=h(l=>r.tagDialogFlagSwithch(),["stop"]))},{default:s(()=>[t(u,null,{default:s(()=>[q]),_:1}),G]),_:1})]),c("div",H,[t(w,{modelValue:a.tagToSearch,"onUpdate:modelValue":e[1]||(e[1]=l=>a.tagToSearch=l),label:"\u30BF\u30B0\u691C\u7D22",clearable:""},null,8,["modelValue"]),t(g,{color:"submit",elevation:"2",disabled:a.tagSerchLoading,onClick:e[2]||(e[2]=h(l=>r.searchTag(),["stop"]))},{default:s(()=>[t(u,null,{default:s(()=>[J]),_:1}),K]),_:1},8,["disabled"])]),c("div",null,[t(y,null,{default:s(()=>[t(T,{cols:"3"},{default:s(()=>[t(g,{variant:"outlined",color:"primary",size:"small",onClick:h(r.clearAllCheck,["stop"])},{default:s(()=>[Q]),_:1},8,["onClick"])]),_:1}),t(T,{cols:"3"}),t(T,{cols:"6"},{default:s(()=>[m(c("input",{type:"checkbox",id:"checked","onUpdate:modelValue":e[3]||(e[3]=l=>a.onlyCheckedFlag=l)},null,512),[[k,a.onlyCheckedFlag]]),W]),_:1})]),_:1})]),m(t(C,null,null,512),[[p,a.tagSerchLoading]]),t(b,{class:"overflow-y-auto mx-auto",width:"100%","max-height":"45vh"},{default:s(()=>[(d(!0),f(N,null,D(a.tagSearchResultList,l=>(d(),A(S,{key:l.id},{default:s(()=>[m(c("input",{type:"checkbox",id:l.id,"onUpdate:modelValue":e[4]||(e[4]=L=>a.checkedTagList=L),value:{id:l.id,name:l.name}},null,8,X),[[k,a.checkedTagList]]),c("label",{for:l.id},R(l.name),9,Y)]),_:2},1024))),128))]),_:1}),F.searchOnly?v("",!0):(d(),f("div",Z,[m(t(g,{class:"longButton my-4",color:"submit",onClick:h(r.createNewTagFlagSwitch,["stop"])},{default:s(()=>[t(u,null,{default:s(()=>[$]),_:1}),ee]),_:1},8,["onClick"]),[[p,!a.createNewTagFlag]]),m(c("div",te,[a.newTagErrorFlag?(d(),f("p",ae,"\u6587\u5B57\u3092\u5165\u529B\u3057\u3066\u304F\u3060\u3055\u3044")):v("",!0),a.tagAlreadyExistsErrorFlag?(d(),f("p",le,"\u305D\u306E\u30BF\u30B0\u306F\u3059\u3067\u306B\u767B\u9332\u3055\u308C\u3044\u307E\u3059")):v("",!0),t(w,{modelValue:a.newTag,"onUpdate:modelValue":e[5]||(e[5]=l=>a.newTag=l),label:"\u65B0\u3057\u3044\u30BF\u30B0"},null,8,["modelValue"]),t(g,{class:"longButton",color:"#BBDEFB",elevation:"2",disabled:a.newTagSending,onClick:e[6]||(e[6]=h(l=>r.createNewTagCheck(),["stop"]))},{default:s(()=>[t(u,null,{default:s(()=>[se]),_:1}),oe]),_:1},8,["disabled"])],512),[[p,a.createNewTagFlag]])]))])]),_:1},8,["modelValue"])])}var de=U(z,[["render",ie],["__scopeId","data-v-517c05f9"]]);export{de as T};
