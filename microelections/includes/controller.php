<?php

declare( strict_types=1 );

namespace MicroElections;

use MicroElections\Entity\Ballot;
use MicroElections\Entity\Election;
use MicroElections\Exception\ValidationException;

class Controller {
	private $path;

	public function __construct( $path ) {
		$this->path = $path;
	}

	public function createAction() {
		Database::cleanup();
		if ( $this->is_post() ) {
			$election = Election::from_form( $_POST );
			Database::persist_entity( $election );

			$this->redirect( 'created', [ 'election' => $election->get_hash() ] );
		}

		return $this->render( 'create', [] );
	}

	public function createdAction() {

		$election = Database::get_election( $_GET['election'] );
		if ( $election === null ) {
			return $this->election_not_found();
		}

		$data = [
			'election'         => $election,
			'participate_link' => add_query_arg( [
				'action'   => 'participate',
				'election' => $election->get_hash(),
			], $this->path ),
			'results_link' => add_query_arg( [
				'action'   => 'results',
				'election' => $election->get_hash(),
				'results_hash' => $election->get_results_hash(),
			], $this->path ),
		];

		return $this->render( 'created', $data );
	}

	public function participateAction() {

		Database::cleanup();

		$election = Database::get_election( $_GET['election'] );
		if ( $election === null ) {
			return $this->election_not_found();
		}

		$error = null;

		if ( $this->is_post() ) {
			try {
				$ballot = Ballot::from_form( $election, $_POST );
				Database::persist_entity( $ballot );

				$this->redirect( 'participated', [
					'election' => $election->get_hash(),
					'ballot'   => $ballot->get_identifier(),
				] );
			} catch ( ValidationException $e ) {
				$error = $e->getMessage();
			}

		}

		return $this->render( 'participate', [ 'election' => $election, 'error' => $error ] );
	}

	public function participatedAction() {
		$election = Database::get_election( $_GET['election'] );
		if ( $election === null ) {
			return $this->election_not_found();
		}

		$ballot_identifier = $_GET['ballot']; // TODO: check

		return $this->render( 'participated', [
			'election'         => $election,
			'ballot_identifier' => $ballot_identifier,
		] );
	}

	public function resultsAction() {

		Database::cleanup();

		$election = Database::get_election( $_GET['election'] );
		if ( $election === null ) {
			return $this->election_not_found();
		}
		if ( $election->get_results_hash() !== $_GET['results_hash'] ) {
			// Correct election hash but invalid results hash
			return $this->election_not_found();
		}

		$ballots = Database::get_ballots( $election );
		$sums = [];
		$abstentions = array_filter( $ballots, function ( Ballot $ballot ) {
			return $ballot->is_abstention();
		} );
		foreach ( $election->get_options() as $option_idx => $option ) {
			$sums[ $option_idx ] = 0;
			foreach ( $ballots as $ballot ) {
				$sums[ $option_idx ] += $ballot->voted_vor( $option_idx ) ? 1 : 0;
			}
		}

		return $this->render( 'results', [
			'election'         => $election,
			'ballots'          => $ballots,
			'sums'             => $sums,
			'ballot_count'     => count( $ballots ),
			'abstention_count' => count( $abstentions ),
		] );
	}

	public function invalidAction(): string {
		return $this->render( 'invalid' );
	}

	private function render( string $template, array $data = [] ) {
		$template = MICROELECTIONS_PATH . 'includes/views/' . $template . '.inc.php';
		if ( ! is_file( $template ) ) {
			// TODO: exception
			return 'Template not found.';
		}

		ob_start();
		extract( $data );
		include $template;

		return ob_get_clean();
	}

	private function election_not_found(): string {
		return $this->render( 'not_found' );
	}

	private function redirect( string $action, array $params = [] ) {
		$params['action'] = $action;
		header( 'Location: ' . $this->link( $action, $params ) );
		exit();
	}

	private function link( string $action, array $params = [] ): string {
		$params['action'] = $action;

		return add_query_arg( $params, $this->path );
	}

	private function is_post() {
		return $_SERVER['REQUEST_METHOD'] === 'POST';
	}
}
