document.addEventListener('DOMContentLoaded', function() {
    
    const CLIENTbtn = document.getElementById('btnCLIENT');
    const VENDORbtn = document.getElementById('btnVENDOR');
    
    const CLIENTform = document.getElementById('CLIENT-FORM');
    const VENDORform = document.getElementById('VENDOR-FORM');

    consumerBtn.addEventListener('click', function() {
        CLIENTform.classList.add('active');
        VENDORform.classList.remove('active');
        CLIENTbtn.classList.add('active');
        VENDORbtn.classList.remove('active');
    });

    merchantBtn.addEventListener('click', function() {
        VENDORbtn.classList.add('active');
        CLIENTform.classList.remove('active');
        VENDORform.classList.add('active');
        CLIENTbtn.classList.remove('active');
   });
});
