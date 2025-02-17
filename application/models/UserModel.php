<?php

defined('BASEPATH') or exit('No Access');
class UserModel extends CI_model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['common_helper']);
    }





    public function getReviewers($filters = [], $limit = 500, $offset = 0, $searchString = '')
    {

        $this->applyReviewerSearchFilter($filters, $searchString);

        $this->db->select('*,"" as password,(SELECT name from countries where id = users.country) as country_name, (SELECT name from states where id = users.state) as state_name,
        IF(profile_image="","",CONCAT("' . base_url('') . '", profile_image)) as profile_image,
        IF(doc1="","",CONCAT("' . base_url('') . '", doc1)) as doc1,
        IF(doc2="","",CONCAT("' . base_url('') . '", doc2)) as doc2,
        IF(doc3="","",CONCAT("' . base_url('') . '", doc3)) as doc3
    ');
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get('users')->result_array();
    }
    public function applyReviewerSearchFilter($filters = [], $searchString = '')
    {
        $searchColumns = ['name','research_area'];
        $filterColumns = ['department','designation','country','state','approval_status'];

        if (!empty($searchString)) {
            $this->db->or_group_start();
            foreach ($searchColumns as $column) {
                $this->db->or_like($column, $searchString);
            }
            $this->db->group_end();
        }

        if (!empty($filters) &&  is_array($filters)) {
            foreach ($filters as $key => $value) {
                if (!in_array($key, $filterColumns) || empty($value)) {
                    continue;
                }
                if (is_numeric($value)) {
                    $this->db->where($key, $value);
                } elseif (is_string($value)) {
                    $this->db->like($key, $value);
                }
            }
        }

        $this->db->where('type', USER_TYPE::REVIEWER);
    }
    public function getReviewersCount($filters = [], $searchString = '')
    {
        $this->applyReviewerSearchFilter($filters, $searchString);
        return $this->db->count_all_results('users');
    }



    public function getJournals($filters = [], $limit = 500, $offset = 0, $searchString = '')
    {

        $this->applyJournalSearchFilter($filters, $searchString);

        $this->db->select('*,"" as password,(SELECT name from countries where id = journals.country) as country_name, (SELECT name from states where id = journals.state) as state_name,CONCAT("' . base_url() . '", image) as image');
        $this->db->order_by('journal_id', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get('journals')->result_array();
    }
    public function applyJournalSearchFilter($filters = [], $searchString = '')
    {
        $searchColumns = ['journal_name','publisher_name','broad_research_area','eissn_no','pissn_no',];
        $filterColumns = ['country', 'publication_type', 'number_of_issue_per_year','review_type','approval_status'];

        if (!empty($searchString)) {
            $this->db->or_group_start();
            foreach ($searchColumns as $column) {
                $this->db->or_like($column, $searchString);
            }
            $this->db->group_end();
        }

        if (!empty($filters) &&  is_array($filters)) {
            foreach ($filters as $key => $value) {
                if (!in_array($key, $filterColumns) || empty($value)) {
                    continue;
                }
                if (is_numeric($value)) {
                    $this->db->where($key, $value);
                } elseif (is_string($value)) {
                    $this->db->like($key, $value);
                }
            }
        }

        // $this->db->where('type',USER_TYPE::REVIEWER);
        //     $query = $this->db->get('journals');

        //    return $query->result_array();
    }
    public function getJournalsCount($filters = [], $searchString = '')
    {
        $this->applyJournalSearchFilter($filters, $searchString);
        return $this->db->count_all_results('journals');
    }




    public function getJournalsByUserId($user_id)
    {
        $this->db->select('*,CONCAT("' . base_url() . '", image) as image');
        $this->db->where("user_id", $user_id);
        $this->db->order_by("journal_id", "DESC");
        $query = $this->db->get('journals');
        return $query->result_array();
    }

    public function deleteJournalById($id, $user_id)
    {
        $this->db->where('journal_id', $id);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('journals');

        if ($query->num_rows() > 0) {
            $this->db->where('journal_id', $id);
            $this->db->where('user_id', $user_id);
            $this->db->delete('journals');

            if ($this->db->affected_rows() > 0) {
                return ['status' => 200, 'message' => 'Journal deleted successfully!'];
            } else {
                return ['status' => 500, 'message' => 'Failed to delete the journal.'];
            }
        } else {
            return ['status' => 404, 'message' => 'No Journal found with the provided ID and User ID.'];
        }
    }

    // public function searchReviewersByName($name)
    // {
    //     $this->db->like('reviewer_name', $name);
    //     $query = $this->db->get('reviewers');

    //     return $query->result_array();
    // }


    public function register($user)
    {
        if (in_array($user['type'], [USER_TYPE::REVIEWER, USER_TYPE::PUBLISHER])) {
            $user['approval_status'] = APPROVAL_STATUS::APPROVED;
        }

        if ($this->db->insert('users', $user)) {
            $new = $this->db->where('id', $this->db->insert_id())->get('users')->row_array();
            unset($new['password']);
            return $new;
        }
        return false;
    }

    public function getUserById($id)
    {
        $this->db->select('*,"" as password,(SELECT name from countries where id = users.country) as country_name, (SELECT name from states where id = users.state) as state_name');
        $user = $this->db->where('id', $id)->get('users')->row_array();
        if (isset($user['password'])) {
            unset($user['password']);
        }
        $user['profile_image'] =  safe_image($user['profile_image']);
        $user['doc1'] = $user['doc1'] != "" ? base_url($user['doc1']) : '';
        $user['doc2'] = $user['doc2'] != "" ? base_url($user['doc2']) : '';
        $user['doc3'] = $user['doc3'] != "" ? base_url($user['doc3']) : '';
        return $user;
    }


    public function updateUserById($id, $data)
    {
        // Exclude sensitive or non-updatable fields
        unset($data['email'], $data['created_at'], $data['password']);

        // Update user details in the database
        $this->db->where('id', $id);
        $this->db->update('users', $data);
        return $this->getUserById($id);
    }


    public function get_journal_by_id($id)
    {
        $this->db->where('journal_id', $id);
        $this->db->select('*,"" as password,(SELECT name from countries where id = journals.country) as country_name, (SELECT name from states where id = journals.state) as state_name,CONCAT("' . base_url() . '", image) as image');
        $query = $this->db->get('journals');
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }

        return null;
    }

    public function get_publication_by_id($id)
    {
        $this->db->where('ppuid', $id);
        $query = $this->db->get('published_papers');
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }

        return null;
    }
    public function get_reviewer_by_id($id)
    {
        $this->db->where('id', $id);
        $this->db->where('type', 'reviewer');
        $this->db->select('*, "" as password, CONCAT("'.base_url().'",profile_image) as profile_image,(Select count(*) from publish_requests where assigned_reviewer ="'.$id.'" and reviewer_remarks !="") as total_reviews');
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }

        return null;
    }
    // public function searchJournalsByName($name)
    // {
    //     $this->db->like('journal_name', $name);
    //     $query = $this->db->get('journals');
    // }
    //     return $query->result_array();
    // }


    public function getCountries()
    {
        return $this->db->get('countries')->result_array();
    }
    public function getStates($countryId)
    {
        $countryId = intval($countryId);
        return $this->db->where('country_id', $countryId)->get('states')->result_array();
    }





    public function insert_journal($data)
    {
        $this->db->insert('journals', $data);
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function update_journal($journal_id, $update_data)
    {

        if ($journal_id && !empty($update_data)) {

            $this->db->where('journal_id', $journal_id);
            $this->db->update('journals', $update_data);


            return true;
        }

        return false;
    }



    public function insert_research_submission($data)
    {
        $this->db->insert('research_papers', $data);
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function insert_publication($data)
    {
        $this->db->insert('published_papers', $data);
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id(); // Return the ID of the inserted row
        }
        return false; // Return false if the insert fails
    }





    public function getPublicationByUserId($id)
    {
        $this->db->where('user_id', $id);
        $this->db->order_by('ppuid', 'DESC');
        $query = $this->db->get('published_papers');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return false;
    }

    public function deletePublicationById($id, $user_id)
    {
        $this->db->where('ppuid', $id);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('published_papers');

        if ($query->num_rows() > 0) {
            $this->db->where('ppuid', $id);
            $this->db->where('user_id', $user_id);
            $this->db->delete('published_papers');

            if ($this->db->affected_rows() > 0) {
                return ['status' => 200, 'message' => 'Publication deleted successfully!'];
            } else {
                return ['status' => 500, 'message' => 'Failed to delete the Publication.'];
            }
        } else {
            return ['status' => 404, 'message' => 'No Publication found with the provided ID and User ID.'];
        }
    }

    public function update_publication($id, $update_data)
    {

        if ($id && !empty($update_data)) {
            $this->db->select('ppuid');
            $this->db->from('published_papers');
            $this->db->where('ppuid', $id);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $this->db->where('ppuid', $id);
                $this->db->update('published_papers', $update_data);
                return true;
            }
            return false;
        }
        return false;
    }

    public function getUserId($userId)
    {
        $query = $this->db->get_where('users', ['id' => $userId]);
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return null;
    }


    public function updateUser($userId, $data)
    {
        $this->db->where('id', $userId);
        return $this->db->update('users', $data);
    }



    public function getPublishRequestsByUserId($id)
    {
        $this->db->select('
            publish_requests.pr_id,
            research_papers.author_name,
            research_papers.paper_title,
            journals.journal_name,
            users.name,
            publish_requests.sender,
            publish_requests.pr_status,
            publish_requests.payment_status,
            publish_requests.live_url,
            publish_requests.created_at,
            publish_requests.updated_at, 
        ');
        $this->db->from('publish_requests');
        $this->db->join('research_papers', 'research_papers.paper_id = publish_requests.paper_id', 'left');
        $this->db->join('users', 'users.id = publish_requests.publisher_id', 'left');
        $this->db->join('journals', 'journals.journal_id = publish_requests.journal_id', 'left');
        $this->db->where('publish_requests.publisher_id', $id);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function update_publish_request_status($id, $status, $live_url = null)
    {
        $this->db->where('pr_id', $id);
        if (!empty($live_url)) {
            $this->db->set('live_url', $live_url);
        }
        $this->db->update('publish_requests', ['pr_status' => $status]);
    }


    public function join_journal($data)
    {
        $this->db->insert('journal_join_requests', $data);
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        }
        return false;
    }


    //   public function getJournalsAndUser($userId) {
    //         $this->db->select('journals.journal_name, journals.status, journals.created_at, users.name AS reviewer_name, users.email AS reviewer_email');
    //         $this->db->from('journals');
    //         $this->db->join('users', 'journals.user_id = users.id');
    //         $this->db->where('journals.user_id', $userId);
    //         $query = $this->db->get();

    //         if ($query->num_rows() > 0) {
    //             return $query->result_array();
    //         }

    //         return false;
    //     }




    public function getJournalsJoinRequests($where = [])
    {
        $this->db->select('
    journal_join_requests.*,
        journals.journal_name, 
        journals.eissn_no,
        journals.pissn_no,
        journals.country, 
        users.name AS reviewer_name, 
        users.email AS reviewer_email,
        users.university_name,
        users.contact As reviewer_contact,
        users.department,
        users.designation,
        users.profile_image,
        CONCAT("' . base_url() . '", users.profile_image) AS profile_image
    ');

        $this->db->from('journal_join_requests');

        $this->db->join('users', 'journal_join_requests.user_id = users.id');
        $this->db->join('journals', 'journal_join_requests.journal_id = journals.journal_id');
        if (!empty($where)) {
            $this->db->where($where);
        }

        $this->db->order_by('journal_join_requests.req_id', 'DESC');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return false;
    }


    public function updateRequestStatus($req_id, $update_data)
    {
        $this->db->where('req_id', $req_id);
        $updated = $this->db->update('journal_join_requests', $update_data);

        if (!$updated) {

            log_message('error', $this->db->last_query());
            log_message('error', $this->db->error());
        }

        return $updated;

    }





    public function getRequestStatus($req_id)
    {
        $this->db->select('approval_status');
        $this->db->from('journal_join_requests');
        $this->db->where('req_id', $req_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->approval_status;
        }

        return null;
    }




    public function getReviewerRequestsById($req_id)
    {
        $this->db->select('*');
        $this->db->from('journal_join_requests');
        $this->db->where('req_id', $req_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return null;
        }
    }



    public function insertJournalReviewerLink($data)
    {
        return $this->db->insert('journal_reviewer_link', $data);
    }


    public function getResearchPaper($filters = [], $limit = 500, $offset = 0, $searchString = '')
    {

        $this->applyResearchPaperFilter($filters, $searchString);

        $this->db->select('*,(SELECT name from countries where id = research_papers.country) as country_name');
        $this->db->order_by('paper_id', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get('research_papers')->result_array();
    }
    public function applyResearchPaperFilter($filters = [], $searchString = '')
    {
        $searchColumns = ['author_name','affiliation','paper_title','keywords'];
        $filterColumns = ['department','country'];

        if (!empty($searchString)) {
            $this->db->or_group_start();
            foreach ($searchColumns as $column) {
                $this->db->or_like($column, $searchString);
            }
            $this->db->group_end();
        }

        if (!empty($filters) &&  is_array($filters)) {
            foreach ($filters as $key => $value) {
                if (!in_array($key, $filterColumns) || empty($value)) {
                    continue;
                }
                if (is_numeric($value)) {
                    $this->db->where($key, $value);
                } elseif (is_string($value)) {
                    $this->db->like($key, $value);
                }
            }
        }

        // $this->db->where('type',USER_TYPE::REVIEWER);
    }
    public function getResearchPaperCount($filters = [], $searchString = '')
    {
        $this->applyResearchPaperFilter($filters, $searchString);
        return $this->db->count_all_results('research_papers');
    }

    public function publisherHasJournal($journal_id, $publisher_id)
    {
        $jid = intval($journal_id);
        $pid = intval($publisher_id);
        return $this->db->where(['journal_id' => $jid,'user_id' => $pid])->count_all_results('journals') > 0;
    }

    public function getPublisherJournals($publisher_id)
    {
        $pid = intval($publisher_id);
        return $this->db->where(['user_id' => $pid])->get('journals')->result_array();
    }

    public function canCreateJoinJournalRequest($journal_id, $user_id)
    {
        $jid = intval($journal_id);
        $uid = intval($user_id);
        $alreadyJoined = $this->db->where(['reviewer_id' => $uid,'journal_id' => $jid])->count_all_results('journal_reviewer_link') > 0;
        if ($alreadyJoined) {
            return false;
        }
        return $this->db->where(['user_id' => $uid,'journal_id' => $jid,'approval_status' => APPROVAL_STATUS::PENDING])->count_all_results('journal_join_requests') == 0;
    }

    public function canCreateJoinPaperRequest($journal_id, $paper_id)
    {
        $jid = intval($journal_id);
        $pid = intval($paper_id);

        return $this->db->where(['paper_id' => $pid,'journal_id' => $jid,'pr_status!=' => PR_STATUS::REJECT])->count_all_results('publish_requests') == 0;
    }

    public function join_paper($data)
    {
        $this->db->insert('publish_requests', $data);
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        }
        return false;
    }
    public function getPaperId($journal_id, $user_id)
    {
        return $this->db->select('paper_id')->from('research_papers')->where('user_id', $user_id)->get()->row('paper_id');
    }



    public function getPublisherPapers($publisher_id)
    {
        $pid = intval($publisher_id);
        return $this->db->where(['publisher_id' => $pid])->get('publish_requests')->result_array();
    }

    public function getResearchPaperRequests($where = [])
    {
        $this->db->select('
        publish_requests.*,
            research_papers.paper_title, 
            users.name,
            journals.journal_name,
            (Select name from users where users.id = publish_requests.assigned_reviewer) AS reviewer_name
        ');
        $this->db->from('publish_requests');
        $this->db->join('users', 'publish_requests.author_id = users.id');
        $this->db->join('journals', 'publish_requests.journal_id = journals.journal_id');
        $this->db->join('research_papers', 'publish_requests.paper_id = research_papers.paper_id');
        if (!empty($where)) {
            $this->db->where($where);
        }
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return false;
    }

    public function getPublishRequestsById($req_id)
    {
        $this->db->select('*');
        $this->db->from('publish_requests');
        $this->db->where('pr_id', $req_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return null;
        }
    }

    public function updatePublishRequestStatus($req_id, $update_data)
    {
        $this->db->where('pr_id', $req_id);
        $updated = $this->db->update('publish_requests', $update_data);

        if (!$updated) {

            log_message('error', $this->db->last_query());
            log_message('error', $this->db->error());
        }

        return $updated;

    }

    public function authorHasPaper($paper_id, $author_id)
    {
        $pid = intval($paper_id);
        $aid = intval($author_id);
        return $this->db->where(['paper_id' => $pid,'user_id' => $aid])->count_all_results('research_papers') > 0;
    }

    public function getJournalById($journal_id)
    {
        $this->db->where("journal_id", $journal_id);
        $query = $this->db->get('journals');
        return $query->row_array();
    }

    public function getPaperById($paper_id)
    {
        $this->db->where("paper_id", $paper_id);
        $query = $this->db->get('research_papers');
        return $query->row_array();
    }





    // joined all journalslist

    public function get_joined_journals($where = [])
    {
        $this->db->select('journal_reviewer_link.*,journals.journal_name,journals.eissn_no,journals.pissn_no,journals.website_link,journals.publication_type, users.*, "" as password, CONCAT("' . base_url() . '", profile_image) as profile_image, (SELECT name from countries where id = users.country) as country_name, (SELECT name from states where id = users.state) as state_name');
        $this->db->from('journal_reviewer_link');


        $this->db->join('users', 'journal_reviewer_link.reviewer_id = users.id');

        $this->db->join('journals', 'journal_reviewer_link.journal_id = journals.journal_id');

        if (!empty($where)) {
            $this->db->where($where);
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return false;

    }




    public function getJournalReviewerLinkByRequestId($req_id)
    {
        $this->db->where("request_id", $req_id);
        $query = $this->db->get('journal_reviewer_link');
        return $query->row_array();
    }









    // leave joined journal

    public function leaveJoinedJournal($requestId)
    {



        $this->db->where('request_id', $requestId);
        $delete = $this->db->delete('journal_reviewer_link');


    }






    public function getresearchpapersByUserId($user_id)
    {
        $this->db->where("user_id", $user_id);
        $this->db->order_by('paper_id', 'DESC');
        $query = $this->db->get('research_papers');
        return $query->result_array();
    }



    public function deleteResearchPaperById($id, $user_id)
    {
        $this->db->where('paper_id', $id);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('research_papers');

        if ($query->num_rows() > 0) {
            $this->db->where('paper_id', $id);
            $this->db->where('user_id', $user_id);
            $this->db->delete('research_papers');

            if ($this->db->affected_rows() > 0) {
                return ['status' => 200, 'message' => 'Research paper deleted successfully!'];
            } else {
                return ['status' => 500, 'message' => 'Failed to delete the journal.'];
            }
        } else {
            return ['status' => 404, 'message' => 'No Research paper found with the provided ID and User ID.'];
        }
    }


    public function update_co_authors($paper_id, $co_authors)
    {

        if (!empty($co_authors)) {

            $this->db->where('paper_id', $paper_id);
            $this->db->update('research_papers', ['co_authors' => json_encode($co_authors)]);
        }
    }





    public function update_research_paper($id, $update_data, $co_authors = null)
    {
        if ($id && !empty($update_data)) {
            $this->db->select('paper_id');
            $this->db->from('research_papers');
            $this->db->where('paper_id', $id);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {

                $this->db->trans_start();


                $this->db->where('paper_id', $id);
                $this->db->update('research_papers', $update_data);


                if (!empty($co_authors)) {
                    $this->update_co_authors($id, $co_authors);
                }

                $this->db->trans_complete();

                return $this->db->trans_status();
            }
            return false;
        }
        return false;
    }

    // public function getPublisherJournals($publisherId)
    // {
    //     return $this->db->select('journal_id')
    //         ->from('journals')
    //         ->where('publisher_id', $publisherId)
    //         ->get()
    //         ->result_array();
    // }

    public function reviewerExists($reviewerId)
    {
        $this->db->select("*");
        $this->db->where('reviewer_id', $reviewerId);
        $query = $this->db->get('journal_reviewer_link');
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return false;
    }
    public function publishRequestExists($pr_id)
    {
        $this->db->select("*");
        $this->db->where('pr_id', $pr_id);
        $query = $this->db->get('publish_requests');
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return false;
    }

    public function getPublishRequest($pr_id)
    {
        $this->db->select("*");
        $this->db->where('pr_id', $pr_id);
        $query = $this->db->get('publish_requests');
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return false;
    }

    public function updatePublishRequest($pr_id, $update_data)
    {
        $reviewer_id = $update_data['assigned_reviewer'];
        $this->db->select('name');
        $this->db->where('id', $reviewer_id);
        $query = $this->db->get('users')->row_array();

        $this->db->where('pr_id', $pr_id);
        $this->db->update('publish_requests', $update_data);
        $update_data['reviewer_name'] = $query['name'];
        return $update_data;
    }



    public function get_research_by_id($id)
    {
        $this->db->where('paper_id', $id);
        $query = $this->db->get('research_papers');
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }

        return null;
    }




    public function get_approved_publication_by_id($id)
    {
        $this->db->where('user_id', $id);
        $this->db->where('approval_status', APPROVAL_STATUS::APPROVED);
        $query = $this->db->get('published_papers');
        $publications = $query->result_array();

        // Count total publications
        $this->db->where('user_id', $id);
        $this->db->where('approval_status', APPROVAL_STATUS::APPROVED);
        $this->db->from('published_papers');
        $total_publications = $this->db->count_all_results();

        return [
            'publications' => $publications,
            'total_publications' => $total_publications
        ];
    }


    // public function get_published_research_papers($journal_id)
    // {
    //     $this->db->select('
    //         rp.paper_id,
    //         rp.author_name,
    //         rp.country,
    //         rp.affiliation,
    //         rp.department,
    //         rp.paper_title,
    //         rp.abstract,
    //         rp.file,
    //         rp.keywords,
    //         rp.user_id,
    //         rp.submission_status,
    //         rp.created_at,
    //         pr.pr_id,
    //         pr.author_id,
    //         pr.journal_id,
    //         pr.publisher_id,
    //         pr.sender,
    //         pr.pr_status,
    //         pr.payment_status,
    //         pr.assigned_reviewer,
    //         pr.reviewer_remarks,
    //         pr.live_url,
    //         pr.created_at AS pr_created_at
    //     ');
    //     $this->db->from('publish_requests pr');
    //     $this->db->join('research_papers rp', 'pr.paper_id = rp.paper_id');
    //     $this->db->where('pr.journal_id', $journal_id);
    //     $this->db->where('pr.pr_status', PR_STATUS::PUBLISHED);

    //     $query = $this->db->get();

    //     return $query->result_array();
    // }


    public function get_published_research_papers($journal_id)
    {
        $this->db->select('
        publish_requests.*,
        research_papers.author_email,
        research_papers.author_contact,
        research_papers.author_name,
        research_papers.country,
        research_papers.affiliation,
        research_papers.department,
        research_papers.paper_title,
        research_papers.abstract,
        research_papers.keywords
    ');
        $this->db->from('publish_requests');
        $this->db->join('research_papers', 'publish_requests.paper_id = research_papers.paper_id');
        $this->db->where('publish_requests.journal_id', $journal_id);
        $this->db->where('publish_requests.pr_status', PR_STATUS::PUBLISHED);

        $query = $this->db->get();

        return $query->result_array();
    }

    public function get_request_by_id($id)
    {
        $this->db->select('publish_requests.* , journals.journal_name ,research_papers.paper_title,research_papers.author_name,research_papers.abstract', );
        $this->db->where('assigned_reviewer', $id);
        $this->db->where('pr_status', PR_STATUS::ACCEPT);
        $this->db->join('journals', 'journals.journal_id = publish_requests.journal_id');
        $this->db->join('research_papers', 'research_papers.paper_id = publish_requests.paper_id');
        $query = $this->db->get('publish_requests');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return null;
    }

    public function update_remarks($pr_id, $id, $remarks)
    {

        if ($pr_id && $id && !empty($remarks)) {
            $this->db->where('pr_id', $pr_id)
                     ->where('assigned_reviewer', $id)
                     ->where('pr_status', PR_STATUS::ACCEPT)
                     ->update('publish_requests', ['reviewer_remarks' => $remarks]);

            return true;
        }

        return false;
    }

    public function addData($result)
    {
        if (!empty($result)) {
            $id = $result['pr_id'];
            $query = $this->db->where('pr_id', $id)->get('transaction');
            if ($query->num_rows() > 0) {
                $existingRecord = $query->row_array();
                if ($existingRecord['status'] == APPROVAL_STATUS::PENDING) {
                    return $existingRecord; //Status Pending -> Record
                } else {
                    return false; // Not pending -> Record
                }
            }
            $inserted = $this->db->insert('transaction', $result);
            if ($inserted) {
                $this->db->where('pr_id', $result['pr_id'])
                ->update('publish_requests', ['payment_status' => PAYMENT_STATUS::PENDING]);
                $res = $this->db->where('pr_id', $result['pr_id'])->get('transaction');
                return $res->row_array();
            } else {
                return false; // Insertion failed
            }
        }

        return false;
    }

    public function getTransactionDetails($id)
    {
        $this->db->where('order_id', $id);
        $this->db->select('*');
        $query = $this->db->get('transaction');
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }

        return null;
    }

    public function markTransactionPaid($order_id, $payment_data)
    {
        $this->db->where('order_id', $order_id);
        $this->db->update('transaction', [
            'status' => PAYMENT_STATUS::COMPLETE,
            'gateway_response'   => $payment_data,
            'updated_at'     => get_datetime()
        ]);

        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;

    }

    public function changePaymentStatus($order_id)
    {
        $this->db->where('order_id', $order_id);
        $query = $this->db->get('transaction');
        if ($query->num_rows() > 0) {
            $transaction = $query->row_array();
            $pr_id = $transaction['pr_id'];
            $this->db->where('pr_id', $pr_id);
            $this->db->update('publish_requests', ['payment_status' => PAYMENT_STATUS::COMPLETE]);
            if ($this->db->affected_rows() > 0) {
                return true;
            }
        }

        return false;
    }
    public function get_reviews_by_reviewer_id($reviewer_id)
    {
        $this->db->select('publish_requests.reviewer_remarks , journals.journal_name ,research_papers.paper_title,research_papers.author_name,research_papers.abstract,research_papers.keywords', );
        $this->db->from('publish_requests');
        $this->db->where('assigned_Reviewer', $reviewer_id);
        $this->db->where('pr_status!=', PR_STATUS::ACCEPT);
        $this->db->join('journals', 'journals.journal_id = publish_requests.journal_id');
        $this->db->join('research_papers', 'research_papers.paper_id = publish_requests.paper_id');

        $query = $this->db->get();
        return $query->result_array();
    }






}
