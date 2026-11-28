@php
    // Palet navy SMA IT Arafah — sama dengan situs publik.
@endphp
<style>
:root{
  --primary-50:#f2f6fb;--primary-100:#e4ecf6;--primary-200:#c6d8ea;--primary-300:#9dbcd8;
  --primary-400:#6c96bd;--primary-500:#487a9f;--primary-600:#2f5c80;--primary-700:#264a67;
  --primary-800:#1f3a5f;--primary-900:#182c47;--primary-950:#101d30;
}

/* ================= latar & kerangka ================= */
.fi-body{background:#f4f7fa}
.fi-main{padding-top:1.35rem;padding-bottom:2.5rem}
.fi-header{margin-bottom:1.1rem}
.fi-header-heading{font-weight:700;letter-spacing:-.015em;color:#14273d;font-size:1.45rem}
.fi-header-subheading{color:#8494a7}

/* ================= sidebar gelap ================= */
.fi-sidebar{background:linear-gradient(180deg,#1f3a5f 0%,#152a42 100%)!important;border-right:0!important;
  box-shadow:4px 0 22px rgba(15,23,42,.10)}
.fi-sidebar-header{background:transparent!important;border-bottom:1px solid rgba(255,255,255,.12)}
.fi-sidebar .fi-logo,.fi-sidebar .fi-sidebar-header a{color:#fff!important;font-weight:800;letter-spacing:-.01em}
.fi-sidebar-nav{padding:.35rem .55rem .8rem}
.fi-sidebar-group-label{color:rgba(255,255,255,.45)!important;font-size:10.5px!important;font-weight:700;
  letter-spacing:.09em;text-transform:uppercase}
.fi-sidebar .fi-sidebar-item-label,.fi-sidebar .fi-sidebar-item-icon,
.fi-sidebar .fi-sidebar-group-btn,.fi-sidebar .fi-sidebar-group-collapse-button{color:rgba(255,255,255,.74)!important}
.fi-sidebar .fi-sidebar-item-btn{border-radius:10px;color:rgba(255,255,255,.86)!important;font-size:.855rem;font-weight:500;
  transition:background .15s,color .15s}
.fi-sidebar .fi-sidebar-item-btn:hover{background:rgba(255,255,255,.09)!important;color:#fff!important}
.fi-sidebar .fi-sidebar-item-btn:hover .fi-sidebar-item-label,
.fi-sidebar .fi-sidebar-item-btn:hover .fi-sidebar-item-icon{color:#fff!important}
.fi-sidebar .fi-sidebar-item.fi-active>.fi-sidebar-item-btn,
.fi-sidebar .fi-sidebar-item-btn[aria-current="page"]{background:rgba(255,255,255,.16)!important;color:#fff!important;
  font-weight:600;box-shadow:inset 2px 0 0 #fdd100}
.fi-sidebar .fi-sidebar-item.fi-active .fi-sidebar-item-label,
.fi-sidebar .fi-sidebar-item.fi-active .fi-sidebar-item-icon{color:#fff!important}
.fi-sidebar .fi-badge{background:rgba(255,255,255,.16);color:#fff}

/* ================= topbar ================= */
.fi-topbar{background:#fff!important;border-bottom:1px solid #e8eef6;box-shadow:0 1px 2px rgba(16,24,40,.03)}
.fi-topbar .fi-topbar-item-label{color:#4a6280;font-weight:600}

/* ================= kartu / section / widget ================= */
.fi-section,.fi-ta-ctn,.fi-wi-stats-overview-stat,.fi-fo-component-ctn,.fi-account-widget{
  background:#fff;border:1px solid #e6edf5!important;border-radius:14px!important;
  box-shadow:0 1px 2px rgba(16,24,40,.04)!important}
.fi-section-header{padding:.85rem 1.1rem}
.fi-section-header-heading{font-size:15px;font-weight:700;color:#243b53}
.fi-section-content{padding:1rem 1.1rem}

/* ================= tabel ================= */
.fi-ta-header-toolbar{padding:.7rem .9rem;border-bottom:1px solid #eef3f9;background:#fbfdff}
.fi-ta-header-cell{font-size:11px!important;font-weight:700!important;letter-spacing:.05em;text-transform:uppercase;color:#8494a7!important}
.fi-ta-row:hover{background:#f7fbff}
.fi-ta-cell{padding:.6rem .85rem!important;font-size:12.8px}
.fi-ta-empty-state{padding:2rem 1rem}

/* ================= tombol & input ================= */
.fi-btn{border-radius:10px!important;font-weight:600}
.fi-btn-color-primary{box-shadow:0 1px 2px rgba(31,58,95,.25)}
.fi-input,.fi-select,.fi-textarea{border-radius:10px!important;border-color:#dfe8f3!important}
.fi-input:focus,.fi-select:focus,.fi-textarea:focus{border-color:#1f3a5f!important;box-shadow:0 0 0 3px rgba(31,58,95,.10)!important}
.fi-badge{border-radius:999px!important;font-weight:600}
.fi-tabs{border-bottom-color:#e8eef6}

/* ================= foto: drop zone ================= */
.fi-fo-file-upload .filepond--root{min-height:96px}
.fi-fo-file-upload .filepond--drop-label{font-size:13.5px;font-weight:600;color:#1f3a5f;text-align:center}
.fi-fo-file-upload .filepond--drop-label label::after{content:" \1F4F7"}

/* ================= HP ================= */
@media(max-width:1023px){
  .fi-main{padding:1rem .8rem 2rem}
  .fi-header-heading{font-size:1.2rem}
}
</style>
