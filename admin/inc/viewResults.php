<?php
$election_id=$_GET['viewResult'];
?>

<div class="row my-3">
    <div class="col-12">
        <h3>Election Results</h3>

        <?php
            $fetchingActiveElection = mysqli_query($db,"SELECT * FROM elections WHERE id='".$election_id."'")or(die(mysqli_error($db)));
            $totalActiveElections=mysqli_num_rows($fetchingActiveElection);

            if($totalActiveElections > 0)
            {
                while($data = mysqli_fetch_assoc($fetchingActiveElection))
                {
                    $election_id = $data['id'];
                    $election_topic = $data['election_topic'];
                    ?>
                        <table class="table">
                    <thead>
                        <tr>
                    <th colspan="4" class="bg-green text-white"><h5>ELECTION TOPIC: <?php echo strtoupper($election_topic) ; ?></h5></th>
                        </tr>
                        <tr>
                            <th>Photo</th>
                            <th>Candidate details</th>
                            <th>No. of voters</th>
                            <!-- <th>Action</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $fetchingCandidates = mysqli_query($db, "SELECT * FROM candidate WHERE election_id='".$election_id."'")or die(mysqli_error($db));

                            while($candidateData =mysqli_fetch_assoc($fetchingCandidates))
                            {
                                $candidate_id = $candidateData['id'];
                                $candidate_photo = $candidateData['candidate_photo'];

                                $fetchingVotes = mysqli_query($db,"SELECT * FROM votings WHERE candidate_id='".$candidate_id."'")or die(mysqli_error($db));
                                $totalVoters= mysqli_num_rows($fetchingVotes);

                                ?>
                                <tr>
                                    <td><img src="<?php echo $candidate_photo; ?>" class=candidate_photo></td>
                                    <td><?php echo "<b>" .$candidateData['candidate_name']. "</b><br />".$candidateData['candidate_details'];?></td>
                                    <td><?php echo $totalVoters;?></td>
                                    
                                    </tr>
                            <?php
                                }
                            ?>
                            </tbody>

                        </table>
                <?php
                    
                    }
                }else {
                    echo "No any active election.";
                }
            ?>


            



        </div>
    </div>
