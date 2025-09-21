<footer class="sticky-footer bg-white mt-auto">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>Copyright &copy; PharmaManage {{ date('Y') }}</span>
        </div>
    </div>
</footer>

<style>
    .sticky-footer {
        padding: 1.5rem 0;
        margin-top: auto;
    }
    
    .copyright {
        font-size: 0.9rem;
        color: var(--secondary-color);
    }
    
    @media (max-width: 576px) {
        .sticky-footer {
            padding: 1rem 0;
        }
        
        .copyright {
            font-size: 0.8rem;
        }
    }
</style>