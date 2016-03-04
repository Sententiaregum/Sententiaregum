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

import InfoBox from '../../../../components/portal/login/InfoBox';
import TestUtils from 'react/lib/ReactTestUtils';
import React from 'react';
import chai from 'chai';

describe('InfoBox', () => {
  it('renders infobox', () => {
    const renderer = TestUtils.createRenderer();
    renderer.render(<InfoBox />);
    const output  = renderer.getRenderOutput();

    chai.expect(output.props.children.props.className).to.equal('info-div-text');
    chai.expect(output.props.children.props.children.props.content).to.equal('pages.portal.login.info_text');
  })
});
