import './bootstrap';

// Modal logic for dashboard Add Task modal
document.addEventListener('DOMContentLoaded', function () {
	const openBtn = document.getElementById('openAddTask');
	const closeBtn = document.getElementById('closeAddTask');
	const cancelBtn = document.getElementById('cancelAddTask');
	const modal = document.getElementById('addTaskModal');
	const firstInput = modal && modal.querySelector('input[name="title"]');

	function show(){
		if(!modal) return;
		modal.classList.remove('hidden');
		modal.classList.add('flex');
		if(firstInput) firstInput.focus();
	}
	function hide(){
		if(!modal) return;
		modal.classList.add('hidden');
		modal.classList.remove('flex');
	}

	if(openBtn) openBtn.addEventListener('click', show);
	if(closeBtn) closeBtn.addEventListener('click', hide);
	if(cancelBtn) cancelBtn.addEventListener('click', hide);

	if(modal){
		modal.addEventListener('click', function(e){
			if(e.target === modal) hide();
		});
	}
});
