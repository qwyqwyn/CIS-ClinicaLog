                <div class="col-md-12">
                  <div class="card">
                    <div class="card-header">
                      <div class="d-flex align-items-center">
                        <h4 class="card-title">Patient</h4>
                        <button
                          class="btn btn-primary btn-round ms-auto"
                          data-bs-toggle="modal"
                          data-bs-target="#addPatientModal"
                          id="addbutton"
                        >
                          <i class="fa fa-plus"></i>
                          Add Patient
                        </button>
                      </div>
                    </div>
                    <div class="card-body">
                      <!-- Modal -->
                      <div
                        class="modal fade"
                        id="addPatientModal"
                        tabindex="-1"
                        role="dialog"
                        aria-hidden="true"
                      >
                        <div class="modal-dialog modal-dialog-centered" role="document" id="AddPatient">
                          <div class="modal-content">
                            <div class="modal-header border-0">
                              <h5 class="modal-title">
                                <span class="fw-mediumbold">Add Patient</span>
                              </h5>
                              <button
                                type="button"
                                class="close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                                id="edit-exit"
                              >
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              <form class="modalButton">
                                <!-- Button for Student Patient -->
                                <a href="addstudent.php">
                                  <button type="button" class="btn btn-primary btn-round ms-auto custom-button" id="addbutton">
                                    Student
                                  </button>
                                </a>
                                <!-- Button for Staff Patient -->
                                <a href="addstaff.php">
                                  <button type="button" class="btn btn-primary btn-round ms-auto custom-button" id="addbutton">
                                    Staff
                                  </button>
                                </a>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="table-responsive">
                        <table id="add-patient" class="table table-striped table-hover">
                          <thead>
                            <tr>
                              <th>Name</th>
                              <th>Patient ID</th>
                              <th>Phone Number</th>
                              <th>Last Visited Date</th>
                              <th>Clinic Staff</th>
                              <th>Reason</th>
                              <th style="width: 10%">Action</th>
                            </tr>
                          </thead>
                          <tfoot>
                            <tr>
                              <th>Name</th>
                              <th>Patient ID</th>
                              <th>Phone Number</th>
                              <th>Last Visited Date</th>
                              <th>Clinic Staff</th>
                              <th>Reason</th>
                              <th>Action</th>
                            </tr>
                          </tfoot>
                          <tbody>
                          <tr>
                            <td>Jackilyn M. Furog</td>
                            <td>2022-00473</td>
                            <td>09756066512</td>
                            <td>06/28/2024</td> 
                            <td>Nurse Tweet</td>
                            <td>Stomach Ache</td>
                            <td>
                              <div class="form-button-action">
                                <button
                                  id="viewButton"
                                  type="button"
                                  data-bs-toggle="tooltip"
                                  title=""
                                  class="btn btn-link btn-primary btn-lg"
                                >
                                  <i class="fa fa-eye"></i>
                                </button>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>