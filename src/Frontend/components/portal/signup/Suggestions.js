/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import Alert                from 'react-bootstrap/lib/Alert';
import React, {PropTypes} from 'react';
import Translate from 'react-translate-component'

/**
 * Presentational component which suggests possible names to the user.
 *
 * @param suggestions
 * @returns {*}
 */
export const Suggestions = ({suggestions}) => {

  if (0 === suggestions.length || !suggestions) {
    return null;
  }

  return (
    <Alert bsStyle="warning">
      <p><Translate content="pages.portal.create_account.suggestions"/></p>
      <ul ref="list">
        {suggestions.map((suggestion, key) => <li key={key}>{suggestion}</li>)}
      </ul>
    </Alert>
  )
};

Suggestions.propTypes = {
  suggestions: PropTypes.arrayOf(PropTypes.string)
};
