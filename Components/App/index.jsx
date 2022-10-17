import React from 'react';
import ReactDOM from 'react-dom';
import apiFetch from '@wordpress/api-fetch';

import TableRow from '../TableRow';

/**
 * The primary App component.
 * @returns {object} - JSX for this component.
 */
class App extends React.Component {
  constructor(props) {
    super(props);

    const { cronData } = window;

    apiFetch.use(apiFetch.createNonceMiddleware(cronData.nonce));

    this.state = {
      cronData: cronData || {},
    };

    this.getRemoteData = this.getRemoteData.bind(this);
  }

  componentDidMount() {
    setTimeout(this.getRemoteData, 20000);
  }

  getRemoteData() {
    apiFetch({ path: '/wp-json/multisite-cron-manager/v1/list' }).then((cronData) => {
      this.setState({
        cronData,
      });
      setTimeout(this.getRemoteData, 20000);
    });
  }

  render() {
    const { cronData } = this.state;

    return (
      <>
        <p>
          As of
          {' '}
          {cronData.dateStamp}
        </p>
        <table className="widefat striped">
          <thead>
            <tr>
              <th>Site</th>
              <th>Next Scheduled Tasks</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            {cronData.rows.map((rowData) => <TableRow key={rowData.id} data={rowData} />)}
          </tbody>
        </table>
      </>
    );
  }
}

// Render the app in the DOM.
ReactDOM.render(
  <App />,
  document.getElementById('mcm-table'),
);
