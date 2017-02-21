/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import Alert                  from 'react-bootstrap/lib/Alert';
import React, { PropTypes }   from 'react';
import Translate              from 'react-translate-component';

/**
 * Presentational component which suggests possible names to the user.
 *
 * @param {Array} suggestions A list of name suggestions to be rendered.
 * @returns {React.Element} The component markup of null.
 */
export const Suggestions = ({ suggestions }) => {

  if (0 === suggestions.length || !suggestions) {
    return null;
  }

  return (
    <Alert bsStyle="warning">
      <p><Translate content="pages.portal.create_account.suggestions" /></p>
      <ul>
        {suggestions.map((suggestion, key) => <li key={key}>{suggestion}</li>)}
      </ul>
    </Alert>
  );
};

Suggestions.propTypes = {
  suggestions: React.PropTypes.arrayOf(React.PropTypes.string)
};
