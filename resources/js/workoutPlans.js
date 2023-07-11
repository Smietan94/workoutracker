import { Modal }          from "bootstrap";
import { get, post, del } from "./ajax";
import DataTable          from "datatables.net";

// TODO po kazdym dodaniu workoutu do kazdego dnia
window.addEventListener('DOMContentLoaded', function() {
    const newWorkoutPlanModal = new Modal(document.getElementById('newWorkoutPlanModal'))

    document.querySelector('.new-workout-plan-btn').addEventListener('click', function(event) {
        openNewWorkoutPlanModal(newWorkoutPlanModal)
    })

    document.querySelector('.save-new-workout-plan-btn').addEventListener('click', function(event) {
        post('/workoutplans', {
            name:             newWorkoutPlanModal._element.querySelector('input[name="name"]').value,
            trainingsPerWeek: newWorkoutPlanModal._element.querySelector('input[name="trainingsPerWeek"]').value,
            notes:            newWorkoutPlanModal._element.querySelector('input[name="notes"]').value
        }, newWorkoutPlanModal._element).then(response => {
            if (response.ok) {
                table.draw()
                document.getElementById('workout-name-input').value = ''
                document.getElementById('training-days-input').value = ''
                document.getElementById('notes-input').value = ''
                newWorkoutPlanModal.hide()
            }
        })
    })

    const table = new DataTable('#workoutPlansTable', {
        serverSide: true,
        ajax: '/workoutplans/load',
        orderMulti: false,
        columns: [
            {data: "name"},
            {data: "notes"},
            {data: "trainingsPerWeek"},
            {data: "createdAt"},
            {
                sortable: false,
                data: row => `
                    <div class="d-flex flex-">
                        <button class="ms-2 btn btn-outline-primary delete-workout-plan-btn"
                                data-id="${ row.id }">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                        <button class="ms-2 btn btn-outline-primary edit-workout-plan-btn"
                                data-id="${ row.id }">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                    </div>
                `
            }
        ]
    })
    
    document.querySelector('#workoutPlansTable').addEventListener('click', function (event) {
        const editBtn   = event.target.closest('.edit-workout-plan-btn')
        const deleteBtn = event.target.closest('.delete-workout-plan-btn')

        if (editBtn) {
            const workoutPlanId = editBtn.getAttribute('data-id')

        } else {
            const workoutPlanId = deleteBtn.getAttribute('data-id')

            if (confirm("Are you sure you want to delete this workout plan?")) {
                del(`/workoutplans/${ workoutPlanId }`).then(() => {
                    table.draw()
                })
            }
        }
    })
})

function openNewWorkoutPlanModal(modal) {
    modal._element.querySelector('.new-workout-plan-btn')

    modal.show()
}