{{-- =============================================
     DataTable Full Screen Loader
     Usage: @include('partials.dt-loader')
     ============================================= --}}

<style>
    #dt-loader-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.45);
        z-index: 99999;
        justify-content: center;
        align-items: center;
    }
    #dt-loader-overlay.active {
        display: flex;
    }
    .dt-loader-box {
        background: transparent;
        border-radius: 12px;
        padding: 30px 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 14px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
    }
    .dt-spinner {
        width: 44px;
        height: 44px;
        border: 4px solid #e0e0e0;
        border-top-color: #4361ee;
        border-radius: 50%;
        animation: dt-spin 0.75s linear infinite;
    }
    @keyframes dt-spin {
        to { transform: rotate(360deg); }
    }
    .dt-loader-text {
        font-size: 14px;
        color: #555;
        font-weight: 500;
        margin: 0;
    }
</style>

<div id="dt-loader-overlay">
    <div class="dt-loader-box">
        <div class="dt-spinner"></div>
        <p class="dt-loader-text">Loading data, please wait...</p>
    </div>
</div>

<script>
    function dtLoaderShow() {
        document.getElementById('dt-loader-overlay').classList.add('active');
    }
    function dtLoaderHide() {
        document.getElementById('dt-loader-overlay').classList.remove('active');
    }
</script>