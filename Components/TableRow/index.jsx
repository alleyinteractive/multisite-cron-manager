import React from 'react';
import PropTypes from 'prop-types';

const TableRow = ({ data }) => (
  <tr>
    <td>{data.domain}</td>
    <td>
      <ol>
        {data.next_jobs.map((job) => (
          <li key={`job-${job.action}-${job.timestamp}`} style={job.diff_dir === '-' ? { color: 'red' } : {}}>
            <code>{job.action}</code>
            <br />
            {job.diff_dir === '-' ? '-' : ''}
            {job.diff}
            {` (${job.human_time})`}
          </li>
        ))}
      </ol>
    </td>
    <td>
      Last Run:
      {` ${data.last_run.action ? data.last_run.action : ''} `}
      <br />
      {data.last_run.timestamp > 0 ? `${data.last_run.diff} ago` : 'N/A'}
    </td>
  </tr>
);

TableRow.propTypes = {
  data: PropTypes.shape({
    id: PropTypes.number,
    domain: PropTypes.string,
    next_jobs: PropTypes.arrayOf(PropTypes.shape({
      timestamp: PropTypes.number,
      action: PropTypes.string,
      diff: PropTypes.string,
      diff_dir: PropTypes.string,
      human_time: PropTypes.string,
    })),
    last_run: PropTypes.shape({
      action: PropTypes.string,
      timestamp: PropTypes.number,
      diff: PropTypes.string,
      diff_dir: PropTypes.string,
      human_time: PropTypes.string,
    }),
  }).isRequired,
};

export default TableRow;
