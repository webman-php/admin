import{a as h,b3 as v,r as a,b as y,f as x,o as H,h as w,j as S,p as R,i as k,n as f,k as e,aZ as u,d8 as z,aJ as C}from"./index.02bbedaf.js";import{u as b}from"./useWindowSizeFn.c5b50549.js";import{u as B}from"./useContentViewHeight.7a5f4790.js";const L=["src"],$=h({__name:"index",props:{frameSrc:v.string.def("")},setup(p){const s=a(!0),d=a(50),i=a(window.innerHeight),r=a(),{headerHeightRef:m}=B(),{prefixCls:o}=y("iframe-page");b(l,150,{immediate:!0});const c=x(()=>({height:`${e(i)}px`}));function l(){const n=e(r);if(!n)return;const t=m.value;d.value=t,i.value=window.innerHeight-t;const g=document.documentElement.clientHeight-t;n.style.height=`${g}px`}function _(){s.value=!1,l()}return(n,t)=>(H(),w("div",{class:f(e(o)),style:u(e(c))},[S(e(z),{spinning:s.value,size:"large",style:u(e(c))},{default:R(()=>[k("iframe",{src:p.frameSrc,class:f(`${e(o)}__main`),ref_key:"frameRef",ref:r,onLoad:_},null,42,L)]),_:1},8,["spinning","style"])],6))}});var V=C($,[["__scopeId","data-v-179381bf"]]);export{V as default};
