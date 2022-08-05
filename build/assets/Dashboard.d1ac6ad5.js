import{B}from"./ApplicationLogo.e6fd2f6b.js";import{o as D,a as z,c as b,r as L,b as d,d as v,e,f as h,w,v as k,g as r,h as n,n as c,u,T as S,i as y,L as x,j as g,t as _,k as M,H as N,l as $,F as j}from"./app.6c1b8228.js";import{_ as A}from"./plugin-vue_export-helper.21dcd24c.js";const E={class:"relative"},H={__name:"Dropdown",props:{align:{default:"right"},width:{default:"48"},contentClasses:{default:()=>["py-1","bg-white"]}},setup(o){const t=o,s=f=>{a.value&&f.key==="Escape"&&(a.value=!1)};D(()=>document.addEventListener("keydown",s)),z(()=>document.removeEventListener("keydown",s));const i=b(()=>({48:"w-48"})[t.width.toString()]),m=b(()=>t.align==="left"?"origin-top-left left-0":t.align==="right"?"origin-top-right right-0":"origin-top"),a=L(!1);return(f,l)=>(d(),v("div",E,[e("div",{onClick:l[0]||(l[0]=p=>a.value=!a.value)},[h(f.$slots,"trigger")]),w(e("div",{class:"fixed inset-0 z-40",onClick:l[1]||(l[1]=p=>a.value=!1)},null,512),[[k,a.value]]),r(S,{"enter-active-class":"transition ease-out duration-200","enter-from-class":"transform opacity-0 scale-95","enter-to-class":"transform opacity-100 scale-100","leave-active-class":"transition ease-in duration-75","leave-from-class":"transform opacity-100 scale-100","leave-to-class":"transform opacity-0 scale-95"},{default:n(()=>[w(e("div",{class:c(["absolute z-50 mt-2 rounded-md shadow-lg",[u(i),u(m)]]),style:{display:"none"},onClick:l[2]||(l[2]=p=>a.value=!1)},[e("div",{class:c(["rounded-md ring-1 ring-black ring-opacity-5",o.contentClasses])},[h(f.$slots,"content")],2)],2),[[k,a.value]])]),_:3})]))}},T={__name:"DropdownLink",setup(o){return(t,s)=>(d(),y(u(x),{class:"block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"},{default:n(()=>[h(t.$slots,"default")]),_:3}))}},V={__name:"NavLink",props:["href","active"],setup(o){const t=o,s=b(()=>t.active?"inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition  duration-150 ease-in-out":"inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out");return(i,m)=>(d(),y(u(x),{href:o.href,class:c(u(s))},{default:n(()=>[h(i.$slots,"default")]),_:3},8,["href","class"]))}},C={__name:"ResponsiveNavLink",props:["href","active"],setup(o){const t=o,s=b(()=>t.active?"block pl-3 pr-4 py-2 border-l-4 border-indigo-400 text-base font-medium text-indigo-700 bg-indigo-50 focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition duration-150 ease-in-out":"block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out");return(i,m)=>(d(),y(u(x),{href:o.href,class:c(u(s))},{default:n(()=>[h(i.$slots,"default")]),_:3},8,["href","class"]))}},O={class:"min-h-screen bg-gray-100"},F={class:"bg-white border-b border-gray-100"},R={class:"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"},U={class:"flex justify-between h-16"},Y={class:"flex"},q={class:"shrink-0 flex items-center"},G={class:"hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"},I=g(" Dashboard "),J={class:"hidden sm:flex sm:items-center sm:ml-6"},K={class:"ml-3 relative"},P={class:"inline-flex rounded-md"},Q={type:"button",class:"inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"},W=e("svg",{class:"ml-2 -mr-0.5 h-4 w-4",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 20 20",fill:"currentColor"},[e("path",{"fill-rule":"evenodd",d:"M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z","clip-rule":"evenodd"})],-1),X=g(" Log Out "),Z={class:"-mr-2 flex items-center sm:hidden"},ee={class:"h-6 w-6",stroke:"currentColor",fill:"none",viewBox:"0 0 24 24"},te={class:"pt-2 pb-3 space-y-1"},se=g(" Dashboard "),oe={class:"pt-4 pb-1 border-t border-gray-200"},ae={class:"px-4"},ne={class:"font-medium text-base text-gray-800"},re={class:"font-medium text-sm text-gray-500"},ie={class:"mt-3 space-y-1"},le=g(" Log Out "),de={key:0,class:"bg-white shadow"},ue={class:"max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8"},ce={__name:"Authenticated",setup(o){const t=L(!1);return(s,i)=>(d(),v("div",null,[e("div",O,[e("nav",F,[e("div",R,[e("div",U,[e("div",Y,[e("div",q,[r(u(x),{href:s.route("dashboard")},{default:n(()=>[r(B,{class:"block h-9 w-auto"})]),_:1},8,["href"])]),e("div",G,[r(V,{href:s.route("dashboard"),active:s.route().current("dashboard")},{default:n(()=>[I]),_:1},8,["href","active"])])]),e("div",J,[e("div",K,[r(H,{align:"right",width:"48"},{trigger:n(()=>[e("span",P,[e("button",Q,[g(_(s.$page.props.auth.user.name)+" ",1),W])])]),content:n(()=>[r(T,{href:s.route("logout"),method:"post",as:"button"},{default:n(()=>[X]),_:1},8,["href"])]),_:1})])]),e("div",Z,[e("button",{onClick:i[0]||(i[0]=m=>t.value=!t.value),class:"inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"},[(d(),v("svg",ee,[e("path",{class:c({hidden:t.value,"inline-flex":!t.value}),"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M4 6h16M4 12h16M4 18h16"},null,2),e("path",{class:c({hidden:!t.value,"inline-flex":t.value}),"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M6 18L18 6M6 6l12 12"},null,2)]))])])])]),e("div",{class:c([{block:t.value,hidden:!t.value},"sm:hidden"])},[e("div",te,[r(C,{href:s.route("dashboard"),active:s.route().current("dashboard")},{default:n(()=>[se]),_:1},8,["href","active"])]),e("div",oe,[e("div",ae,[e("div",ne,_(s.$page.props.auth.user.name),1),e("div",re,_(s.$page.props.auth.user.email),1)]),e("div",ie,[r(C,{href:s.route("logout"),method:"post",as:"button"},{default:n(()=>[le]),_:1},8,["href"])])])],2)]),s.$slots.header?(d(),v("header",de,[e("div",ue,[h(s.$slots,"header")])])):M("",!0),e("main",null,[h(s.$slots,"default")])])]))}},he={data(){return{message:"aa"}},components:{Head:N,BreezeAuthenticatedLayout:ce},methods:{async submit(){await axios.get("api/test").then(o=>{console.log(o),this.message=o.data.message})},async postSubmit(){await axios.post("api/postTest",{message:"post ok"},{withCredentials:!0}).then(o=>{this.message=o.data.message})}}},me=e("h2",{class:"font-semibold text-xl text-gray-800 leading-tight"}," Dashboard ",-1),fe={class:"py-12"},pe={class:"max-w-7xl mx-auto sm:px-6 lg:px-8"},ge={class:"bg-white overflow-hidden shadow-sm sm:rounded-lg"},ve=e("div",{class:"p-6 bg-white border-b border-gray-200"}," You're logged in! ",-1),_e=e("br",null,null,-1),be=e("br",null,null,-1);function xe(o,t,s,i,m,a){const f=$("Head"),l=$("BreezeAuthenticatedLayout");return d(),v(j,null,[r(f,{title:"Dashboard"}),r(l,null,{header:n(()=>[me]),default:n(()=>[e("div",fe,[e("div",pe,[e("div",ge,[ve,e("button",{onClick:t[0]||(t[0]=(...p)=>a.submit&&a.submit(...p))},"push"),_e,e("button",{onClick:t[1]||(t[1]=(...p)=>a.postSubmit&&a.postSubmit(...p))},"postSubmit"),be,g(" "+_(m.message),1)])])])]),_:1})],64)}var $e=A(he,[["render",xe]]);export{$e as default};
