import { Modal }          from "bootstrap"
import { get, post, del } from "./ajax"
import DataTable          from "datatables.net"

window.addEventListener('DOMContentLoaded', function () {
    const editCategoryModal = new Modal(document.getElementById('editCategoryModal'))
    // const newCategoryModal  = new Modal(document.getElementById('newCategoryModal'))

    // document.querySelector('.new-category-btn').addEventListener('click', function (event) {
    //     openNewCategoryModal(newCategoryModal)
    // })

    // document.querySelector('.save-new-category-btn').addEventListener('click', function(event) {
    //     post(`/categories`, {
    //         name: newCategoryModal._element.querySelector('input[name="name"]').value
    //     }, newCategoryModal._element).then(response => {
    //         if (response.ok) {
    //             table.draw()
    //             document.getElementById('new-category-name').value = ''
    //             newCategoryModal.hide()
    //         }
    //     })
    // })

    const table = new DataTable('#categoriesTable', {
        serverSide: true,
        ajax: '/categories/load',
        orderMulti: false,
        columns: [
            // {data: "name"},
            {data: row => `
                <a href="/exercises/${ row.id }">${ row.name }</a>
            `},
            {data: "createdAt"},
            {data: "updatedAt"},
            {
                sortable: false,
                data: row => `
                    <div class="d-flex dlex-">
                        <button class="ms-2 btn btn-outline-primary edit-category-btn"
                                data-id="${ row.id }">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                    </div>
                `
            }
        ]
    })

    document.querySelector('#categoriesTable').addEventListener('click', function (event) {
        const editBtn   = event.target.closest('.edit-category-btn')
        const deleteBtn = event.target.closest('.delete-category-btn') 

        if (editBtn) {
            const categoryId = editBtn.getAttribute('data-id')

            get(`/categories/${ categoryId }`)
                .then(response => response.json())
                .then(response => openEditCategoryModal(editCategoryModal, response))
        } else {
            const categoryId = deleteBtn.getAttribute('data-id')

            if (confirm('Are you sure you want to delete this category?')) {
                del(`/categories/${ categoryId }`).then(() => {
                    table.draw()
                })
            }
        }
    })

    document.querySelector('.update-category-btn').addEventListener('click', function (event) {
        const categoryId = event.currentTarget.getAttribute('data-id')

        post(`/categories/${ categoryId }`, {
            name: editCategoryModal._element.querySelector('input[name="name"]').value
        }, editCategoryModal._element).then(response => {
            if (response.ok) {
                table.draw()
                editCategoryModal.hide()
            }
        })
    })
})

function openEditCategoryModal(modal, {id, name}) {
    const nameInput = modal._element.querySelector('input[name="name"]')

    nameInput.value = name

    modal._element.querySelector('.update-category-btn').setAttribute('data-id', id)

    modal.show()
}

// function openNewCategoryModal(modal) {
//     modal._element.querySelector('.new-category-btn')

//     modal.show()
// }


{/* <button type="submit" class="ms-2 btn btn-outline-primary delete-category-btn"
data-id="${ row.id }">
<i class="bi bi-trash3-fill"></i>
</button> */}