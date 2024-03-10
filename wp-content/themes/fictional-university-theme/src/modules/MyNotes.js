import $ from "jquery";

class MyNotes {
  constructor() {
    this.events();
  }

  events() {
    $("#my-notes").on("click", ".delete-note", this.deleteNote);
    //$(".delete-note").on("click", this.deleteNote); // when page 1st load only watching for delete-note and edit note class
    $("#my-notes").on("click", ".edit-note", this.editNote.bind(this));
    $("#my-notes").on("click", ".update-note", this.updateNote.bind(this));
    $(".submit-note").on("click", this.createNote.bind(this));
  }

  // Methods will go here
  // Edit note btn function
  editNote(e) {
    var thisNote = $(e.target).parents("li");

    if (thisNote.data("state") == "editable") {
      this.makeNoteReadOnly(thisNote);
    } else {
      this.makeNoteEditable(thisNote);
    }
  }

  makeNoteEditable(thisNote) {
    thisNote
      .find(".edit-note")
      .html('<i class="fa fa-times" aria-hidden="true"></i> Cancel');
    thisNote
      .find(".note-title-field, .note-body-field")
      .removeAttr("readonly")
      .addClass("note-active-field");
    thisNote.find(".update-note").addClass("update-note--visible");
    thisNote.data("state", "editable");
  }

  makeNoteReadOnly(thisNote) {
    thisNote
      .find(".edit-note")
      .html('<i class="fa fa-pencil" aria-hidden="true"></i> Edit');
    thisNote
      .find(".note-title-field, .note-body-field")
      .attr("readonly", "readonly")
      .removeClass("note-active-field");
    thisNote.find(".update-note").removeClass("update-note--visible");
    thisNote.data("state", "cancel");
  }

  // Delete note btn function
  deleteNote(e) {
    var thisNote = $(e.target).parents("li"); // to determent what element id has been clicked
    // ajax is a jquery tool, a method that is a great option when you want to able to control what type of request u are sending
    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
      },
      url:
        universityData.root_url + "/wp-json/wp/v2/note/" + thisNote.data("id"), // property root part of the url
      type: "DELETE", // type of request
      success: (response) => {
        thisNote.slideUp(); // jquery method removes the element from the page using a slide up animation
        console.log("congrats"), console.log(response);
        // code for disappearring alert message in notes if it reach the limit
        if (response.userNoteCount < 8) {
          $(".note-limit-message").removeClass("active");
        }
      }, // function we want to run if the request is successful
      error: (response) => {
        console.log("sorry"), console.log(response);
      }, // function we want to run if the request is unsuccessful
    });
  }

  // Update or save note btn function
  updateNote(e) {
    var thisNote = $(e.target).parents("li"); // to determent what element id has been clicked
    var ourUpdatedPost = {
      title: thisNote.find(".note-title-field").val(),
      content: thisNote.find(".note-body-field").val(),
    };

    // ajax is a jquery tool, a method that is a great option when you want to able to control what type of request u are sending
    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
      },
      url:
        universityData.root_url + "/wp-json/wp/v2/note/" + thisNote.data("id"), // property root part of the url
      type: "POST", // type of request
      data: ourUpdatedPost,
      success: (response) => {
        this.makeNoteReadOnly(thisNote);
        console.log("congrats"), console.log(response);
      }, // function we want to run if the request is successful
      error: (response) => {
        console.log("sorry"), console.log(response);
      }, // function we want to run if the request is unsuccessful
    });
  }

  // Create note btn function --create http request type
  createNote(e) {
    var ourNewPost = {
      title: $(".new-note-title").val(),
      content: $(".new-note-body").val(),
      status: "publish",
      //status: "private" // not advisable
    };

    // ajax is a jquery tool, a method that is a great option when you want to able to control what type of request u are sending
    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
      },
      url: universityData.root_url + "/wp-json/wp/v2/note/", // property root part of the url
      type: "POST", // type of request
      data: ourNewPost,
      success: (response) => {
        $(".new-note-title, .new-note-body").val("");
        $(`
          <li data-id="${response.id}">
            <input readonly class="note-title-field" value="${response.title.raw}">
            <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</span>
            <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</span>
            <textarea readonly class="note-body-field">
              ${response.content.raw}
            </textarea>
            <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i> Save</span>
          </li>
        `)
          .prependTo("#my-notes")
          .hide()
          .slideDown();
        console.log("congrats"), console.log(response);
      }, // function we want to run if the request is successful
      error: (response) => {
        if (response.responseText == "You have reached your note limit.") {
          $(".note-limit-message").addClass("active");
        }
        console.log("sorry"), console.log(response);
      }, // function we want to run if the request is unsuccessful
    });
  }
}

export default MyNotes;
