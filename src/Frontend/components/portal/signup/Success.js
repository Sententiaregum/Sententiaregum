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

import React from 'react';
import DismissableAlertBox from '../../app/markup/DismissableAlertBox';
import Translate from 'react-translate-component';

/**
 * Success component.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class Success extends React.Component {
  /**
   * Render.
   *
   * @returns {React.Element} The markup.
   */
  render() {
    return (
      <DismissableAlertBox bsStyle="success">
        <p><Translate ref="textbox" content="pages.portal.create_account.success" /></p>
      </DismissableAlertBox>
    );
  }
}
