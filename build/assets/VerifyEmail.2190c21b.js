import{C as u,c as f,b as a,i as _,h as s,g as i,u as e,H as h,d as p,k as g,e as o,n as y,L as b,x as k,j as n}from"./app.6c1b8228.js";import{_ as v,a as x}from"./Guest.803a93c1.js";import"./ApplicationLogo.e6fd2f6b.js";import"./plugin-vue_export-helper.21dcd24c.js";const w=o("div",{class:"mb-4 text-sm text-gray-600"}," Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another. ",-1),V={key:0,class:"mb-4 font-medium text-sm text-green-600"},B=["onSubmit"],C={class:"mt-4 flex items-center justify-between"},E=n(" Resend Verification Email "),L=n("Log Out"),$={__name:"VerifyEmail",props:{status:String},setup(r){const c=r,t=u(),d=()=>{t.post(route("verification.send"))},l=f(()=>c.status==="verification-link-sent");return(m,N)=>(a(),_(v,null,{default:s(()=>[i(e(h),{title:"Email Verification"}),w,e(l)?(a(),p("div",V," A new verification link has been sent to the email address you provided during registration. ")):g("",!0),o("form",{onSubmit:k(d,["prevent"])},[o("div",C,[i(x,{class:y({"opacity-25":e(t).processing}),disabled:e(t).processing},{default:s(()=>[E]),_:1},8,["class","disabled"]),i(e(b),{href:m.route("logout"),method:"post",as:"button",class:"underline text-sm text-gray-600 hover:text-gray-900"},{default:s(()=>[L]),_:1},8,["href"])])],40,B)]),_:1}))}};export{$ as default};
