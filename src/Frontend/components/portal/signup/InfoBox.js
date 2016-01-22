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
import Translate from 'react-translate-component';
import DismissableAlertBox from '../../app/markup/DismissableAlertBox';

/**
 * InfoBox component that is shown at the top of the registration form.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class InfoBox extends React.Component {
  /**
   * Renders the info box.
   *
   * @returns {React.Element} The react dom markup.
   */
  render() {
    return (
      <DismissableAlertBox bsStyle="info">
        <Translate ref="textbox" content="pages.portal.create_account.info_box" />
      </DismissableAlertBox>
    );
  }
}
