import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
const { __ } = wp.i18n;

const AnnualReceiptRow = ( { annualReceipt } ) => {
	const { year, amount, count, statementUrl } = annualReceipt[ 1 ];

	return (
		<div className="give-donor-profile-table__row">
			<div className="give-donor-profile-table__column">
				{ year.label }
			</div>
			<div className="give-donor-profile-table__column">
				{ amount.formatted }
			</div>
			<div className="give-donor-profile-table__column">
				{ count }
			</div>
			<div className="give-donor-profile-table__column">
				<a href={ statementUrl }>
					{ __( 'View Receipt', 'give' ) } <FontAwesomeIcon icon="arrow-right" />
				</a>
			</div>
		</div>
	);
};

export default AnnualReceiptRow;